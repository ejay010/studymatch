<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\EducatorProfile;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create a few users and educator profiles
        $user1 = User::factory()->create([
            'name' => 'Mrs. Smith',
            'email' => 'smith@example.com',
        ]);

        EducatorProfile::create([
            'user_id' => $user1->id,
            'bio' => 'Experienced 4th grade math and science teacher. I specialize in making learning fun!',
            'qualifications' => ['B.Ed', 'State Certified'],
            'hourly_rate' => 45.00,
            'is_verified' => true,
        ]);

        $user2 = User::factory()->create([
            'name' => 'Mr. Johnson',
            'email' => 'johnson@example.com',
        ]);

        EducatorProfile::create([
            'user_id' => $user2->id,
            'bio' => 'High school history and reading tutor. Helping students prepare for college.',
            'qualifications' => ['M.A. History'],
            'hourly_rate' => 60.00,
            'is_verified' => true,
        ]);

        $user3 = User::factory()->create([
            'name' => 'Ms. Davis',
            'email' => 'davis@example.com',
        ]);

        EducatorProfile::create([
            'user_id' => $user3->id,
            'bio' => 'Special education certified. I focus on early reading and 1st grade math fundamentals.',
            'qualifications' => ['Special Ed Certification'],
            'hourly_rate' => 50.00,
            'is_verified' => true,
        ]);
    }
}
