<?php

use App\Models\User;
use App\Models\EducatorProfile;
use App\Models\StudentProfile;
use App\Models\Booking;
use App\Models\GroupClass;
use App\Models\DigitalResource;
use App\Models\Transaction;
use App\Models\ResourcePurchase;
use App\Models\ClassEnrollment;
use App\Agents\CommerceAgent;
use Carbon\Carbon;

it('determines dynamic commission rates based on educator subscription tier and product type', function () {
    $agent = new CommerceAgent();

    $educatorUser = User::factory()->create(['role' => 'educator']);
    $freeEducator = EducatorProfile::create(['user_id' => $educatorUser->id, 'tier' => 'free']);
    $proEducator = EducatorProfile::create(['user_id' => User::factory()->create()->id, 'tier' => 'professional']);
    $premiumEducator = EducatorProfile::create(['user_id' => User::factory()->create()->id, 'tier' => 'premium']);

    $booking = Booking::create([
        'educator_profile_id' => $freeEducator->id,
        'type' => '1-on-1',
        'status' => 'pending',
        'starts_at' => now()->addDay(),
        'ends_at' => now()->addDay()->addHour(),
        'max_capacity' => 1,
    ]);

    $resource = DigitalResource::create([
        'educator_profile_id' => $freeEducator->id,
        'title' => 'Sample Worksheet',
        'subject' => 'Math',
        'price' => 10.00,
        'file_path' => 'resources/sample.pdf',
        'file_name' => 'sample.pdf',
    ]);

    // Service commissions (1-on-1 and Group Classes)
    expect($agent->determineCommissionRate($booking, $freeEducator))->toBe(0.20);
    expect($agent->determineCommissionRate($booking, $proEducator))->toBe(0.10);
    expect($agent->determineCommissionRate($booking, $premiumEducator))->toBe(0.05);

    // Digital resource commissions
    expect($agent->determineCommissionRate($resource, $freeEducator))->toBe(0.25);
    expect($agent->determineCommissionRate($resource, $proEducator))->toBe(0.10);
    expect($agent->determineCommissionRate($resource, $premiumEducator))->toBe(0.10);
});

it('processes a transaction and initiates checkout with accurate platform fees', function () {
    $parent = User::factory()->create(['role' => 'parent']);
    $educatorUser = User::factory()->create(['role' => 'educator']);
    $educator = EducatorProfile::create([
        'user_id' => $educatorUser->id,
        'tier' => 'professional', // 10% fee
        'hourly_rate' => 50.00,
    ]);

    $booking = Booking::create([
        'educator_profile_id' => $educator->id,
        'type' => '1-on-1',
        'status' => 'pending',
        'starts_at' => now()->addDay(),
        'ends_at' => now()->addDay()->addHour(),
        'max_capacity' => 1,
    ]);

    $agent = new CommerceAgent();
    $result = $agent->processTransaction(
        buyerId: $parent->id,
        purchasable: $booking,
        amount: 50.00,
        educator: $educator
    );

    expect($result)->toHaveKey('transaction');
    expect($result)->toHaveKey('checkout_url');

    $transaction = $result['transaction'];
    expect($transaction->status)->toBe('pending');
    expect($transaction->amount)->toBe('50.00');
    expect($transaction->commission_amount)->toBe('5.00'); // 10% of 50
    expect($transaction->educator_payout)->toBe(45.00);
});

it('captures an order and fulfills a digital resource purchase', function () {
    $parent = User::factory()->create(['role' => 'parent']);
    $educatorUser = User::factory()->create(['role' => 'educator']);
    $educator = EducatorProfile::create(['user_id' => $educatorUser->id, 'tier' => 'free']);

    $resource = DigitalResource::create([
        'educator_profile_id' => $educator->id,
        'title' => 'Biology Study Guide',
        'subject' => 'Science',
        'price' => 12.00,
        'file_path' => 'resources/bio.pdf',
        'file_name' => 'bio.pdf',
    ]);

    $agent = new CommerceAgent();
    $init = $agent->processTransaction($parent->id, $resource, 12.00, $educator);
    $transaction = $init['transaction'];

    // Capture the mock order
    $capturedTx = $agent->captureOrder($transaction->payment_provider_id);

    expect($capturedTx->status)->toBe('completed');

    // Verify ownership record created
    $this->assertDatabaseHas('resource_purchases', [
        'digital_resource_id' => $resource->id,
        'user_id' => $parent->id,
        'price_paid' => 12.00,
    ]);
});

it('handles the return callback route and redirects to parent dashboard', function () {
    $parent = User::factory()->create(['role' => 'parent']);
    $educatorUser = User::factory()->create(['role' => 'educator']);
    $educator = EducatorProfile::create(['user_id' => $educatorUser->id]);

    $groupClass = GroupClass::create([
        'educator_profile_id' => $educator->id,
        'title' => 'Robotics 101',
        'subject' => 'Coding & Tech',
        'starts_at' => now()->addDays(3),
        'price' => 40.00,
        'max_capacity' => 6,
    ]);

    $agent = new CommerceAgent();
    $init = $agent->processTransaction($parent->id, $groupClass, 40.00, $educator);
    $orderId = $init['transaction']->payment_provider_id;

    $response = $this->actingAs($parent)->get(route('payment.paypal.return', ['token' => $orderId]));

    $response->assertRedirect(route('dashboard.parent'));
    $response->assertSessionHas('status', 'Payment successful! Your order has been confirmed.');

    expect($init['transaction']->fresh()->status)->toBe('completed');
    $this->assertDatabaseHas('class_enrollments', [
        'group_class_id' => $groupClass->id,
        'user_id' => $parent->id,
        'status' => 'confirmed',
    ]);
});

it('reflects completed transactions in the educator earnings and financial ledger', function () {
    $educatorUser = User::factory()->create(['role' => 'educator']);
    $educator = EducatorProfile::create([
        'user_id' => $educatorUser->id,
        'tier' => 'free',
        'hourly_rate' => 100.00,
    ]);

    $parent = User::factory()->create(['role' => 'parent']);

    $booking = Booking::create([
        'educator_profile_id' => $educator->id,
        'type' => '1-on-1',
        'status' => 'confirmed',
        'starts_at' => now()->addDay(),
        'ends_at' => now()->addDay()->addHour(),
        'max_capacity' => 1,
    ]);

    Transaction::create([
        'user_id' => $parent->id,
        'purchasable_id' => $booking->id,
        'purchasable_type' => Booking::class,
        'amount' => 100.00,
        'commission_amount' => 20.00, // 20% free tier
        'status' => 'completed',
        'payment_provider_id' => 'TX_TEST_123',
    ]);

    $response = $this->actingAs($educatorUser)->get(route('dashboard.educator'));
    $response->assertStatus(200);
    $response->assertSee('$80.00'); // $100 gross - $20 platform fee
    $response->assertSee('Platform Fee: $20.00');
});
