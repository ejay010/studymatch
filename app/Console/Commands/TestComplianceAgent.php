<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Agents\ComplianceAgent;
use App\Models\EducatorProfile;
use App\Models\User;
use App\Models\Transaction;

class TestComplianceAgent extends Command
{
    protected $signature = 'app:test-compliance';
    protected $description = 'Simulate the Compliance Agent workflow';

    public function handle()
    {
        $this->info("Starting Compliance Agent Test...\n");

        $agent = new ComplianceAgent();

        // 1. Create a new unverified educator
        $this->info("1. Creating a new unverified educator (Mr. Test)...");
        $user = User::factory()->create(['name' => 'Mr. Test', 'email' => 'test' . uniqid() . '@example.com']);
        $educator = EducatorProfile::create([
            'user_id' => $user->id,
            'bio' => 'A brand new tutor on the platform.',
            'hourly_rate' => 30.00,
            'is_verified' => false,
        ]);
        $this->line("Educator created. Verified Status: " . ($educator->is_verified ? 'Yes' : 'No'));

        // 2. Initiate Background Check
        $this->info("\n2. Initiating Background Check with Checkr...");
        $candidateId = $agent->initiateBackgroundCheck($educator);
        $this->line("Checkr Candidate ID generated: " . $candidateId);

        // 3. Simulate Webhook Success
        $this->info("\n3. Simulating Checkr 'clear' webhook response...");
        $agent->handleBackgroundCheckResult($educator, 'clear');
        $this->line("Educator Verified Status: " . ($educator->fresh()->is_verified ? 'Yes' : 'No') . " \u{2705}");

        // 4. Test Upsell Logic
        $this->info("\n4. Testing Upsell Logic...");
        $this->line("Creating \$600 worth of transactions to trigger the upsell...");
        Transaction::create([
            'user_id' => $user->id,
            'purchasable_id' => $educator->id,
            'purchasable_type' => EducatorProfile::class,
            'amount' => 600,
            'commission_amount' => 150, // 25% free tier
            'status' => 'completed',
            'created_at' => now()->subDays(2)
        ]);

        $this->line("Running evaluateForUpsell()...");
        $agent->evaluateForUpsell($educator);
        $this->line("Upsell logic executed successfully! \u{2705}");
        
        $this->info("\nTest Complete!");
    }
}
