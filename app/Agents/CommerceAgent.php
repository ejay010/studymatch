<?php

namespace App\Agents;

use App\Models\Transaction;
use App\Models\EducatorProfile;
use App\Services\PayPalConnector;
use App\Services\CreatePayPalOrderRequest;

class CommerceAgent
{
    const TIER_FREE_COMMISSION = 0.25; // 25% for free tier on resources
    const TIER_PRO_COMMISSION = 0.10;  // 10% for premium tier

    /**
     * Process a transaction for an Educator (e.g., booking a class or buying a resource)
     */
    public function processTransaction(int $buyerId, $purchasable, float $amount, EducatorProfile $educator)
    {
        // 1. Calculate Commission
        $commissionRate = $this->determineCommissionRate($educator);
        $commissionAmount = round($amount * $commissionRate, 2);

        // 2. Create Transaction Record
        $transaction = Transaction::create([
            'user_id' => $buyerId,
            'purchasable_id' => $purchasable->id,
            'purchasable_type' => get_class($purchasable),
            'amount' => $amount,
            'commission_amount' => $commissionAmount,
            'status' => 'pending',
        ]);

        // 3. Trigger PayPal Order Creation via Saloon
        $connector = new PayPalConnector();
        $request = new CreatePayPalOrderRequest($amount, "StudyMatch Purchase: " . get_class($purchasable));
        
        $response = $connector->send($request);
        
        if ($response->failed()) {
            throw new \Exception("PayPal Error: " . $response->body());
        }

        $paypalData = $response->json();
        
        // Update transaction with PayPal ID
        $transaction->update(['payment_provider_id' => $paypalData['id']]);

        // Find the 'approve' link from PayPal's response to redirect the user
        $approveLink = collect($paypalData['links'])->firstWhere('rel', 'approve')['href'] ?? null;

        return [
            'transaction' => $transaction,
            'checkout_url' => $approveLink
        ];
    }

    /**
     * Determine the correct commission rate dynamically
     */
    private function determineCommissionRate(EducatorProfile $educator): float
    {
        // Example logic: if they pay a monthly subscription, lower their commission rate
        // (In a full app, we'd check a `subscription_tier` column)
        return self::TIER_FREE_COMMISSION; 
    }

    /**
     * Process a recurring subscription for a Parent/Student ($9.99/mo).
     */
    public function processParentSubscription(int $buyerId, \App\Models\StudentProfile $student)
    {
        // 1. Process recurring payment via PayPal (using Billing Agreements in real app)
        // 2. Mark student as premium
        $student->update(['is_premium' => true]);

        return "Subscription activated for Student Profile #" . $student->id;
    }

    /**
     * Fulfill a physical resource (e.g. books, flashcards).
     */
    public function fulfillPhysicalGood(Transaction $transaction, \App\Models\PhysicalGood $good, string $shippingAddress)
    {
        if ($transaction->status !== 'completed') {
            throw new \Exception("Cannot ship unpaid physical good.");
        }

        // Generate a shipping label via API (e.g., Shippo, EasyPost)
        $trackingNumber = "TRACK_" . uniqid();

        // Decrement stock
        if ($good->stock_quantity > 0) {
            $good->decrement('stock_quantity');
        }

        return "Order shipped! Tracking: " . $trackingNumber;
    }
}
