<?php

namespace App\Agents;

use App\Models\EducatorProfile;
use App\Models\Transaction;
use App\Services\CheckrConnector;
use App\Services\CreateCheckrCandidateRequest;

class ComplianceAgent
{
    /**
     * Trigger a background check for a new educator.
     */
    public function initiateBackgroundCheck(EducatorProfile $educator)
    {
        $connector = new CheckrConnector();
        $request = new CreateCheckrCandidateRequest(
            firstName: "Test", // In reality, fetch from $educator->user->first_name
            lastName: "Teacher",
            email: $educator->user->email
        );
        
        // Disable real API call for now to prevent errors without keys
        // $response = $connector->send($request);
        // $candidateId = $response->json('id');
        
        $candidateId = "mock_cand_" . uniqid();
        
        // We would store this candidateId on the EducatorProfile
        return $candidateId;
    }

    /**
     * Called by a webhook when Checkr finishes a background check.
     */
    public function handleBackgroundCheckResult(EducatorProfile $educator, string $status)
    {
        if ($status === 'clear') {
            $educator->update(['is_verified' => true]);
            // Re-index to scout so they show up in searches
            $educator->searchable();
        }
    }

    /**
     * Evaluate if a Free Tier educator is making so much money that they 
     * should upgrade to the Professional or Premium tier.
     */
    public function evaluateForUpsell(EducatorProfile $educator)
    {
        // Calculate total earnings in the last 30 days
        $thirtyDaysAgo = now()->subDays(30);
        $totalSales = Transaction::whereHasMorph('purchasable', [EducatorProfile::class]) // simplistic view
            ->where('status', 'completed')
            ->where('created_at', '>=', $thirtyDaysAgo)
            ->sum('amount');

        // If they sold over $500 this month, the 25% free tier fee ($125) 
        // is much worse than the $19.99/mo Pro tier (10% fee = $50 + $20 = $70).
        if ($totalSales > 500) {
            $this->sendUpsellNotification($educator);
        }
    }

    private function sendUpsellNotification(EducatorProfile $educator)
    {
        // Logic to send an email or in-app notification:
        // "You could have saved $X this month by switching to Pro!"
    }
}
