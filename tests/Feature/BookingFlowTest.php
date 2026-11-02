<?php

use App\Models\User;
use App\Models\EducatorProfile;
use App\Models\StudentProfile;
use App\Models\EducatorAvailability;
use App\Models\Booking;
use App\Agents\SchedulingAgent;
use App\Livewire\EducatorAvailabilityForm;
use App\Livewire\BookSession;
use Livewire\Livewire;
use Carbon\Carbon;

it('allows an educator to configure and save their weekly availability', function () {
    $user = User::factory()->create(['role' => 'educator']);
    $educator = EducatorProfile::create([
        'user_id' => $user->id,
        'hourly_rate' => 50.00,
        'bio' => 'Math tutor',
    ]);

    $this->actingAs($user);

    Livewire::test(EducatorAvailabilityForm::class)
        ->set('days.1.is_available', true)
        ->set('days.1.start_time', '10:00')
        ->set('days.1.end_time', '15:00')
        ->set('days.6.is_available', false)
        ->call('save')
        ->assertSet('isSaved', true);

    $this->assertDatabaseHas('educator_availabilities', [
        'educator_profile_id' => $educator->id,
        'day_of_week' => 1,
        'is_available' => true,
        'start_time' => '10:00:00',
        'end_time' => '15:00:00',
    ]);
});

it('computes available slots and excludes conflicting bookings', function () {
    $user = User::factory()->create(['role' => 'educator']);
    $educator = EducatorProfile::create([
        'user_id' => $user->id,
        'hourly_rate' => 60.00,
        'bio' => 'Science teacher',
    ]);

    // Set Monday availability: 09:00 to 12:00 (3 one-hour slots: 9-10, 10-11, 11-12)
    EducatorAvailability::create([
        'educator_profile_id' => $educator->id,
        'day_of_week' => 1, // Monday
        'start_time' => '09:00:00',
        'end_time' => '12:00:00',
        'is_available' => true,
    ]);

    // Future Monday
    $targetMonday = Carbon::now()->next(Carbon::MONDAY)->format('Y-m-d');

    // Create a conflict for 10:00 - 11:00
    Booking::create([
        'educator_profile_id' => $educator->id,
        'type' => '1-on-1',
        'status' => 'confirmed',
        'starts_at' => $targetMonday . ' 10:00:00',
        'ends_at' => $targetMonday . ' 11:00:00',
        'max_capacity' => 1,
    ]);

    $agent = new SchedulingAgent();
    $slots = $agent->getAvailableSlots($educator, $targetMonday);

    expect($slots)->toHaveCount(3);
    
    // First slot 9-10 should be available
    expect($slots[0]['formatted_time'])->toBe('9:00 AM - 10:00 AM');
    expect($slots[0]['is_available'])->toBeTrue();

    // Second slot 10-11 has conflict, should not be available
    expect($slots[1]['formatted_time'])->toBe('10:00 AM - 11:00 AM');
    expect($slots[1]['is_available'])->toBeFalse();

    // Third slot 11-12 should be available
    expect($slots[2]['formatted_time'])->toBe('11:00 AM - 12:00 PM');
    expect($slots[2]['is_available'])->toBeTrue();
});

it('prevents double-booking via SchedulingAgent', function () {
    $educatorUser = User::factory()->create(['role' => 'educator']);
    $educator = EducatorProfile::create([
        'user_id' => $educatorUser->id,
        'hourly_rate' => 45.00,
    ]);

    $parentUser = User::factory()->create(['role' => 'parent']);
    $student = StudentProfile::create(['user_id' => $parentUser->id]);

    $targetDate = Carbon::now()->addDays(5)->format('Y-m-d');
    $startsAt = $targetDate . ' 14:00:00';
    $endsAt = $targetDate . ' 15:00:00';

    $agent = new SchedulingAgent();
    
    // First booking succeeds
    $booking = $agent->schedule1on1($educator->id, $student->id, $startsAt, $endsAt);
    expect($booking)->toBeInstanceOf(Booking::class);
    expect($booking->status)->toBe('confirmed');

    // Second booking on overlapping slot must throw exception
    expect(fn () => $agent->schedule1on1($educator->id, $student->id, $startsAt, $endsAt))
        ->toThrow(\Exception::class, "The educator is already booked for this time slot.");
});

it('allows a parent to book a slot via the BookSession Livewire component', function () {
    $educatorUser = User::factory()->create(['role' => 'educator', 'name' => 'Dr. Marie Curie']);
    $educator = EducatorProfile::create([
        'user_id' => $educatorUser->id,
        'hourly_rate' => 75.00,
        'bio' => 'Physics and Chemistry',
    ]);

    // Next Wednesday
    $targetDate = Carbon::now()->next(Carbon::WEDNESDAY)->format('Y-m-d');
    $dayOfWeek = Carbon::parse($targetDate)->dayOfWeek;

    EducatorAvailability::create([
        'educator_profile_id' => $educator->id,
        'day_of_week' => $dayOfWeek,
        'start_time' => '13:00:00',
        'end_time' => '16:00:00',
        'is_available' => true,
    ]);

    $parent = User::factory()->create(['role' => 'parent']);
    $this->actingAs($parent);

    $slotKey = $targetDate . ' 13:00:00|' . $targetDate . ' 14:00:00';

    Livewire::test(BookSession::class, ['educator' => $educator])
        ->set('selectedDate', $targetDate)
        ->call('selectSlot', $slotKey)
        ->call('book')
        ->assertSet('successMessage', 'Session booked successfully!')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('bookings', [
        'educator_profile_id' => $educator->id,
        'type' => '1-on-1',
        'status' => 'confirmed',
        'starts_at' => $targetDate . ' 13:00:00',
        'ends_at' => $targetDate . ' 14:00:00',
    ]);
});

it('displays upcoming sessions on both educator and parent dashboards', function () {
    $educatorUser = User::factory()->create(['role' => 'educator', 'name' => 'Prof. Einstein']);
    $educator = EducatorProfile::create([
        'user_id' => $educatorUser->id,
        'hourly_rate' => 90.00,
        'bio' => 'Relativity',
    ]);

    $parentUser = User::factory()->create(['role' => 'parent', 'name' => 'Sarah Connor']);
    $student = StudentProfile::create(['user_id' => $parentUser->id, 'grade_level' => 10]);

    $futureSession = Carbon::now()->addDays(2)->setHour(14)->setMinute(0)->setSecond(0);

    Booking::create([
        'educator_profile_id' => $educator->id,
        'student_profile_id' => $student->id,
        'type' => '1-on-1',
        'status' => 'confirmed',
        'starts_at' => $futureSession,
        'ends_at' => $futureSession->copy()->addHour(),
        'meeting_link' => 'https://meet.google.com/test-session-123',
        'max_capacity' => 1,
    ]);

    // Check Educator Dashboard
    $educatorResponse = $this->actingAs($educatorUser)->get('/dashboard/educator');
    $educatorResponse->assertStatus(200);
    $educatorResponse->assertSee("Sarah Connor's Student");
    $educatorResponse->assertSee("Join Classroom");

    // Check Parent Dashboard
    $parentResponse = $this->actingAs($parentUser)->get('/dashboard/parent');
    $parentResponse->assertStatus(200);
    $parentResponse->assertSee("Prof. Einstein");
    $parentResponse->assertSee("Join Classroom");
});
