<?php

use App\Models\User;
use App\Models\EducatorProfile;
use App\Models\StudentProfile;
use App\Models\GroupClass;
use App\Models\ClassEnrollment;
use App\Agents\SchedulingAgent;
use App\Livewire\EducatorGroupClasses;
use App\Livewire\GroupClassCatalog;
use Livewire\Livewire;
use Carbon\Carbon;

it('allows an educator to create a group class via Livewire', function () {
    $user = User::factory()->create(['role' => 'educator']);
    $educator = EducatorProfile::create([
        'user_id' => $user->id,
        'hourly_rate' => 50.00,
    ]);

    $this->actingAs($user);

    $futureDate = Carbon::now()->addDays(5)->format('Y-m-d\TH:i');

    Livewire::test(EducatorGroupClasses::class)
        ->set('title', 'Grade 4 Science Club')
        ->set('description', 'Fun hands-on experiments.')
        ->set('subject', 'Science')
        ->set('grade_level', 4)
        ->set('price', 30.00)
        ->set('starts_at', $futureDate)
        ->set('duration_minutes', 60)
        ->set('max_capacity', 5)
        ->call('save')
        ->assertSet('successMessage', 'Small group class created successfully!');

    $this->assertDatabaseHas('group_classes', [
        'educator_profile_id' => $educator->id,
        'title' => 'Grade 4 Science Club',
        'subject' => 'Science',
        'grade_level' => 4,
        'price' => 30.00,
        'max_capacity' => 5,
    ]);
});

it('displays upcoming group classes on the public catalog', function () {
    $user = User::factory()->create(['name' => 'Prof. McGonagall', 'role' => 'educator']);
    $educator = EducatorProfile::create([
        'user_id' => $user->id,
        'hourly_rate' => 60.00,
        'is_verified' => true,
    ]);

    $class = GroupClass::create([
        'educator_profile_id' => $educator->id,
        'title' => 'Intro to Python Coding',
        'description' => 'Learn programming basics.',
        'subject' => 'Coding & Tech',
        'grade_level' => 6,
        'price' => 25.00,
        'starts_at' => Carbon::now()->addDays(3),
        'duration_minutes' => 60,
        'max_capacity' => 6,
    ]);

    $response = $this->get(route('classes.index'));
    $response->assertStatus(200);
    $response->assertSee('Intro to Python Coding');
    $response->assertSee('Prof. McGonagall');
    $response->assertSee('Coding & Tech');
    $response->assertSee('6 spots left');
});

it('filters classes by subject and search keyword in catalog', function () {
    $user = User::factory()->create(['role' => 'educator']);
    $educator = EducatorProfile::create(['user_id' => $user->id]);

    GroupClass::create([
        'educator_profile_id' => $educator->id,
        'title' => 'Algebra 1 Mastery',
        'subject' => 'Math',
        'starts_at' => Carbon::now()->addDays(2),
        'max_capacity' => 6,
        'price' => 20,
    ]);

    GroupClass::create([
        'educator_profile_id' => $educator->id,
        'title' => 'Creative Writing Workshop',
        'subject' => 'English & Reading',
        'starts_at' => Carbon::now()->addDays(2),
        'max_capacity' => 6,
        'price' => 20,
    ]);

    Livewire::test(GroupClassCatalog::class)
        ->set('selectedSubject', 'Math')
        ->assertSee('Algebra 1 Mastery')
        ->assertDontSee('Creative Writing Workshop')
        ->set('selectedSubject', '')
        ->set('search', 'Writing')
        ->assertSee('Creative Writing Workshop')
        ->assertDontSee('Algebra 1 Mastery');
});

it('allows a parent to enroll a student and strictly enforces max capacity', function () {
    $educatorUser = User::factory()->create(['role' => 'educator']);
    $educator = EducatorProfile::create(['user_id' => $educatorUser->id]);

    // Create a class capped at 2 students
    $class = GroupClass::create([
        'educator_profile_id' => $educator->id,
        'title' => 'Micro Cohort Math',
        'subject' => 'Math',
        'starts_at' => Carbon::now()->addDays(4),
        'max_capacity' => 2,
        'price' => 15.00,
    ]);

    $parent1 = User::factory()->create(['role' => 'parent']);
    $student1 = StudentProfile::create(['user_id' => $parent1->id]);

    $parent2 = User::factory()->create(['role' => 'parent']);
    $student2 = StudentProfile::create(['user_id' => $parent2->id]);

    $parent3 = User::factory()->create(['role' => 'parent']);
    $student3 = StudentProfile::create(['user_id' => $parent3->id]);

    $agent = new SchedulingAgent();

    // Student 1 enrolls successfully
    $enrollment1 = $agent->enrollInGroupClass($class->id, $student1->id, $parent1->id);
    expect($enrollment1)->toBeInstanceOf(ClassEnrollment::class);
    expect($class->fresh()->remaining_spots)->toBe(1);

    // Student 2 enrolls successfully
    $enrollment2 = $agent->enrollInGroupClass($class->id, $student2->id, $parent2->id);
    expect($enrollment2)->toBeInstanceOf(ClassEnrollment::class);
    expect($class->fresh()->remaining_spots)->toBe(0);
    expect($class->fresh()->is_full)->toBeTrue();

    // Student 3 attempts to enroll, should fail because class is full
    expect(fn () => $agent->enrollInGroupClass($class->id, $student3->id, $parent3->id))
        ->toThrow(\Exception::class, "This class is full. Maximum capacity of 2 students reached.");

    // Student 1 attempts to enroll a second time, should fail
    expect(fn () => $agent->enrollInGroupClass($class->id, $student1->id, $parent1->id))
        ->toThrow(\Exception::class, "This student is already enrolled in this class.");
});

it('displays enrolled classes on the parent dashboard', function () {
    $educatorUser = User::factory()->create(['role' => 'educator', 'name' => 'Neil deGrasse Tyson']);
    $educator = EducatorProfile::create(['user_id' => $educatorUser->id]);

    $class = GroupClass::create([
        'educator_profile_id' => $educator->id,
        'title' => 'Astrophysics for Kids',
        'subject' => 'Science',
        'starts_at' => Carbon::now()->addDays(3),
        'max_capacity' => 6,
        'price' => 35.00,
        'meeting_link' => 'https://meet.google.com/astro-class',
    ]);

    $parentUser = User::factory()->create(['role' => 'parent']);
    $student = StudentProfile::create(['user_id' => $parentUser->id]);

    ClassEnrollment::create([
        'group_class_id' => $class->id,
        'student_profile_id' => $student->id,
        'user_id' => $parentUser->id,
        'status' => 'confirmed',
    ]);

    $response = $this->actingAs($parentUser)->get(route('dashboard.parent'));
    $response->assertStatus(200);
    $response->assertSee('Astrophysics for Kids');
    $response->assertSee('Neil deGrasse Tyson');
    $response->assertSee('https://meet.google.com/astro-class');
});
