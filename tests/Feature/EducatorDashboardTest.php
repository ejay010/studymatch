<?php

use App\Models\User;
use App\Models\EducatorProfile;

it('allows educators to view their dashboard', function () {
    $user = User::factory()->create(['role' => 'educator']);
    $educator = EducatorProfile::create([
        'user_id' => $user->id,
        'bio' => 'Teaching is my passion.',
        'hourly_rate' => 55.00,
        'is_verified' => true,
    ]);

    $response = $this->actingAs($user)->get('/dashboard/educator');

    $response->assertStatus(200);
    $response->assertSee('Educator Dashboard');
    $response->assertSee('55.00/hr');
});

it('prevents parents from viewing the educator dashboard', function () {
    $user = User::factory()->create(['role' => 'parent']);

    $response = $this->actingAs($user)->get('/dashboard/educator');

    $response->assertStatus(403);
});
