<?php

use App\Models\EducatorProfile;
use App\Models\User;

it('displays the public educator profile', function () {
    $user = User::factory()->create(['name' => 'Professor Plum']);
    $educator = EducatorProfile::create([
        'user_id' => $user->id,
        'bio' => 'Teaching is my passion.',
        'hourly_rate' => 55.00,
        'is_verified' => true,
    ]);

    $response = $this->get('/tutor/' . $educator->id);

    $response->assertStatus(200);
    $response->assertSee('Professor Plum');
    $response->assertSee('Teaching is my passion.');
    $response->assertSee('55.00/hr');
    $response->assertSee('Book a 1-on-1 Session');
});

it('returns 404 for a non-existent educator', function () {
    $response = $this->get('/tutor/99999');
    
    $response->assertStatus(404);
});
