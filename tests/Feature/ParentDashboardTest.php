<?php

use App\Models\User;
use App\Models\StudentProfile;

it('allows parents to view their dashboard', function () {
    $user = User::factory()->create(['role' => 'parent']);
    $student = StudentProfile::create([
        'user_id' => $user->id,
        'is_premium' => true,
    ]);

    $response = $this->actingAs($user)->get('/dashboard/parent');

    $response->assertStatus(200);
    $response->assertSee('Parent Dashboard');
    $response->assertSee('Manage Subscription');
});

it('prevents educators from viewing the parent dashboard', function () {
    $user = User::factory()->create(['role' => 'educator']);

    $response = $this->actingAs($user)->get('/dashboard/parent');

    $response->assertStatus(403);
});
