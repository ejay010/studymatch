<?php

use App\Models\User;
use App\Models\EducatorProfile;
use App\Models\StudentProfile;
use App\Models\DigitalResource;
use App\Models\ResourcePurchase;
use App\Livewire\EducatorDigitalResources;
use App\Livewire\ResourceStore;
use Livewire\Livewire;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

it('allows an educator to upload a digital resource via Livewire', function () {
    Storage::fake('local');

    $user = User::factory()->create(['role' => 'educator']);
    $educator = EducatorProfile::create(['user_id' => $user->id]);

    $this->actingAs($user);

    $fakeFile = UploadedFile::fake()->create('geometry_packet.pdf', 1024, 'application/pdf');

    Livewire::test(EducatorDigitalResources::class)
        ->set('title', 'High School Geometry Packet')
        ->set('description', 'Comprehensive 20-page geometry worksheet.')
        ->set('subject', 'Math')
        ->set('grade_level', 9)
        ->set('price', 8.50)
        ->set('file', $fakeFile)
        ->call('save')
        ->assertSet('successMessage', 'Digital resource uploaded and published to the marketplace!');

    $this->assertDatabaseHas('digital_resources', [
        'educator_profile_id' => $educator->id,
        'title' => 'High School Geometry Packet',
        'file_name' => 'geometry_packet.pdf',
        'file_type' => 'pdf',
        'price' => 8.50,
    ]);
});

it('displays resources on the public store and filters by subject and price', function () {
    $user = User::factory()->create(['name' => 'Teacher Jane', 'role' => 'educator']);
    $educator = EducatorProfile::create(['user_id' => $user->id]);

    DigitalResource::create([
        'educator_profile_id' => $educator->id,
        'title' => 'Solar System Flashcards',
        'subject' => 'Science',
        'grade_level' => 3,
        'price' => 0.00,
        'file_path' => 'resources/solar.pdf',
        'file_name' => 'solar.pdf',
    ]);

    DigitalResource::create([
        'educator_profile_id' => $educator->id,
        'title' => 'Calculus 1 Cheat Sheet',
        'subject' => 'Math',
        'grade_level' => 12,
        'price' => 5.00,
        'file_path' => 'resources/calc.pdf',
        'file_name' => 'calc.pdf',
    ]);

    $response = $this->get(route('resources.index'));
    $response->assertStatus(200);
    $response->assertSee('Solar System Flashcards');
    $response->assertSee('Calculus 1 Cheat Sheet');
    $response->assertSee('Teacher Jane');

    // Filter by free
    Livewire::test(ResourceStore::class)
        ->set('priceFilter', 'free')
        ->assertSee('Solar System Flashcards')
        ->assertDontSee('Calculus 1 Cheat Sheet')
        ->set('priceFilter', 'paid')
        ->assertSee('Calculus 1 Cheat Sheet')
        ->assertDontSee('Solar System Flashcards');
});

it('allows free resources to be downloaded and blocks unauthorized paid downloads', function () {
    Storage::fake('local');

    $educatorUser = User::factory()->create(['role' => 'educator']);
    $educator = EducatorProfile::create(['user_id' => $educatorUser->id]);

    Storage::disk('local')->put('resources/free_item.pdf', 'Free Content');
    $freeResource = DigitalResource::create([
        'educator_profile_id' => $educator->id,
        'title' => 'Free Phonics Guide',
        'subject' => 'English & Reading',
        'price' => 0.00,
        'file_path' => 'resources/free_item.pdf',
        'file_name' => 'free_item.pdf',
    ]);

    Storage::disk('local')->put('resources/paid_item.pdf', 'Paid Secret Content');
    $paidResource = DigitalResource::create([
        'educator_profile_id' => $educator->id,
        'title' => 'Mastering SAT Writing',
        'subject' => 'English & Reading',
        'price' => 15.00,
        'file_path' => 'resources/paid_item.pdf',
        'file_name' => 'paid_item.pdf',
    ]);

    $parentUser = User::factory()->create(['role' => 'parent']);

    // 1. Downloading free resource succeeds
    $freeResponse = $this->actingAs($parentUser)->get(route('resources.download', $freeResource->id));
    $freeResponse->assertStatus(200);
    expect($freeResource->fresh()->downloads_count)->toBe(1);

    // 2. Downloading unpurchased paid resource fails with 403
    $paidResponse = $this->actingAs($parentUser)->get(route('resources.download', $paidResource->id));
    $paidResponse->assertStatus(403);

    // 3. Creator educator can always download their own paid resource
    $educatorResponse = $this->actingAs($educatorUser)->get(route('resources.download', $paidResource->id));
    $educatorResponse->assertStatus(200);

    // 4. After purchasing, parent can download
    ResourcePurchase::create([
        'digital_resource_id' => $paidResource->id,
        'user_id' => $parentUser->id,
        'price_paid' => 15.00,
    ]);

    $purchasedResponse = $this->actingAs($parentUser)->get(route('resources.download', $paidResource->id));
    $purchasedResponse->assertStatus(200);
});

it('allows premium parent subscribers to download paid resources for free', function () {
    Storage::fake('local');

    $educatorUser = User::factory()->create(['role' => 'educator']);
    $educator = EducatorProfile::create(['user_id' => $educatorUser->id]);

    Storage::disk('local')->put('resources/premium_eligible.pdf', 'Premium Content');
    $paidResource = DigitalResource::create([
        'educator_profile_id' => $educator->id,
        'title' => 'AP Chemistry Lab Manual',
        'subject' => 'Science',
        'price' => 20.00,
        'file_path' => 'resources/premium_eligible.pdf',
        'file_name' => 'premium_eligible.pdf',
    ]);

    $premiumParent = User::factory()->create(['role' => 'parent']);
    StudentProfile::create([
        'user_id' => $premiumParent->id,
        'is_premium' => true,
    ]);

    $response = $this->actingAs($premiumParent)->get(route('resources.download', $paidResource->id));
    $response->assertStatus(200);
});

it('displays purchased resources on the parent dashboard', function () {
    $educatorUser = User::factory()->create(['role' => 'educator', 'name' => 'Dr. Robert']);
    $educator = EducatorProfile::create(['user_id' => $educatorUser->id]);

    $resource = DigitalResource::create([
        'educator_profile_id' => $educator->id,
        'title' => 'Spanish Vocabulary Flashcards',
        'subject' => 'English & Reading',
        'price' => 7.00,
        'file_path' => 'resources/spanish.pdf',
        'file_name' => 'spanish.pdf',
        'file_size' => 204800,
    ]);

    $parentUser = User::factory()->create(['role' => 'parent']);
    ResourcePurchase::create([
        'digital_resource_id' => $resource->id,
        'user_id' => $parentUser->id,
        'price_paid' => 7.00,
    ]);

    $response = $this->actingAs($parentUser)->get(route('dashboard.parent'));
    $response->assertStatus(200);
    $response->assertSee('Spanish Vocabulary Flashcards');
    $response->assertSee('Dr. Robert');
});
