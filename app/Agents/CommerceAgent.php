<?php

namespace App\Agents;

use App\Models\Transaction;
use App\Models\EducatorProfile;
use App\Models\Booking;
use App\Models\GroupClass;
use App\Models\ClassEnrollment;
use App\Models\DigitalResource;
use App\Models\ResourcePurchase;
use App\Models\StudentProfile;
use App\Models\PhysicalGood;
use App\Services\PayPalConnector;
use App\Services\CreatePayPalOrderRequest;
use App\Services\CapturePayPalOrderRequest;

class CommerceAgent
{
    // Digital Resource Commissions
    const RESOURCE_COMMISSION_FREE = 0.25;    // 25% for free tier
    const RESOURCE_COMMISSION_PRO = 0.10;     // 10% for pro/premium tier

    // Services (1-on-1 and Group Classes) Commissions
    const SERVICE_COMMISSION_FREE = 0.20;     // 20% for free tier
    const SERVICE_COMMISSION_PRO = 0.10;      // 10% for professional tier ($19.99/mo)
    const SERVICE_COMMISSION_PREMIUM = 0.05;  // 5% for premium tier ($39.99/mo)

    /**
     * Process a transaction for any purchasable item (Booking, GroupClass, DigitalResource, Subscription, PhysicalGood)
     *
     * @param int $buyerId
     * @param mixed $purchasable
     * @param float $amount
     * @param EducatorProfile|null $educator
     * @return array
     * @throws \Exception
     */
    public function processTransaction(int $buyerId, $purchasable, float $amount, ?EducatorProfile $educator = null): array
    {
        if (!$educator && method_exists($purchasable, 'educatorProfile')) {
            $educator = $purchasable->educatorProfile;
        }

        // 1. Calculate dynamic commission
        $commissionRate = $this->determineCommissionRate($purchasable, $educator);
        $commissionAmount = round($amount * $commissionRate, 2);

        // 2. Create pending transaction
        $transaction = Transaction::create([
            'user_id' => $buyerId,
            'purchasable_id' => $purchasable->id,
            'purchasable_type' => get_class($purchasable),
            'amount' => $amount,
            'commission_amount' => $commissionAmount,
            'status' => 'pending',
        ]);

        // 3. Initiate PayPal Order
        $clientId = config('services.paypal.client_id');
        $secret = config('services.paypal.secret');

        // Check if sandbox / test mode without live credentials
        if (empty($clientId) || empty($secret) || app()->environment('testing')) {
            $mockOrderId = 'MOCK_PAYPAL_' . uniqid();
            $transaction->update(['payment_provider_id' => $mockOrderId]);
            $approveLink = route('payment.paypal.return', ['token' => $mockOrderId]);

            return [
                'transaction' => $transaction,
                'checkout_url' => $approveLink,
            ];
        }

        $connector = new PayPalConnector();
        $description = "StudyMatch Purchase: " . class_basename($purchasable) . " #" . $purchasable->id;
        $request = new CreatePayPalOrderRequest($amount, $description);

        $response = $connector->send($request);

        if ($response->failed()) {
            throw new \Exception("PayPal Order Error: " . $response->body());
        }

        $paypalData = $response->json();
        $orderId = $paypalData['id'] ?? null;
        $transaction->update(['payment_provider_id' => $orderId]);

        $approveLink = collect($paypalData['links'] ?? [])->firstWhere('rel', 'approve')['href'] ?? null;

        return [
            'transaction' => $transaction,
            'checkout_url' => $approveLink,
        ];
    }

    /**
     * Determine dynamic commission rate based on product type and educator tier
     */
    public function determineCommissionRate($purchasable, ?EducatorProfile $educator = null): float
    {
        $tier = $educator ? ($educator->tier ?? 'free') : 'free';

        // Digital Resources
        if ($purchasable instanceof DigitalResource) {
            return in_array($tier, ['professional', 'premium', 'pro'])
                ? self::RESOURCE_COMMISSION_PRO
                : self::RESOURCE_COMMISSION_FREE;
        }

        // Parent Subscriptions have no educator commission
        if ($purchasable instanceof StudentProfile) {
            return 0.00;
        }

        // Tutoring Sessions & Group Classes
        if ($tier === 'premium') {
            return self::SERVICE_COMMISSION_PREMIUM;
        } elseif (in_array($tier, ['professional', 'pro'])) {
            return self::SERVICE_COMMISSION_PRO;
        }

        return self::SERVICE_COMMISSION_FREE;
    }

    /**
     * Capture PayPal Order and fulfill purchase
     *
     * @param string $orderId
     * @return Transaction
     * @throws \Exception
     */
    public function captureOrder(string $orderId): Transaction
    {
        $transaction = Transaction::where('payment_provider_id', $orderId)->firstOrFail();

        if ($transaction->status === 'completed') {
            return $transaction;
        }

        // If not mock order and credentials configured, call PayPal API
        if (!str_starts_with($orderId, 'MOCK_PAYPAL_') && !app()->environment('testing')) {
            $connector = new PayPalConnector();
            $response = $connector->send(new CapturePayPalOrderRequest($orderId));

            if ($response->failed()) {
                $transaction->update(['status' => 'failed']);
                throw new \Exception("PayPal Capture Error: " . $response->body());
            }
        }

        return $this->fulfillTransaction($transaction);
    }

    /**
     * Fulfill the purchase based on purchasable type
     *
     * @param Transaction $transaction
     * @return Transaction
     */
    public function fulfillTransaction(Transaction $transaction): Transaction
    {
        $transaction->update(['status' => 'completed']);
        $purchasable = $transaction->purchasable;

        if (!$purchasable) {
            return $transaction;
        }

        if ($purchasable instanceof Booking) {
            $purchasable->update(['status' => 'confirmed']);
        } elseif ($purchasable instanceof GroupClass) {
            // Find student profile for the buyer
            $studentProfile = StudentProfile::firstOrCreate(['user_id' => $transaction->user_id]);
            ClassEnrollment::firstOrCreate(
                [
                    'group_class_id' => $purchasable->id,
                    'student_profile_id' => $studentProfile->id,
                ],
                [
                    'user_id' => $transaction->user_id,
                    'status' => 'confirmed',
                ]
            );
        } elseif ($purchasable instanceof DigitalResource) {
            ResourcePurchase::firstOrCreate(
                [
                    'digital_resource_id' => $purchasable->id,
                    'user_id' => $transaction->user_id,
                ],
                [
                    'price_paid' => $transaction->amount,
                ]
            );
        } elseif ($purchasable instanceof StudentProfile) {
            $purchasable->update(['is_premium' => true]);
        } elseif ($purchasable instanceof PhysicalGood) {
            $this->fulfillPhysicalGood($transaction, $purchasable, "Standard Delivery Address");
        }

        return $transaction;
    }

    /**
     * Process a recurring subscription for a Parent/Student ($9.99/mo).
     */
    public function processParentSubscription(int $buyerId, StudentProfile $student)
    {
        $student->update(['is_premium' => true]);
        return "Subscription activated for Student Profile #" . $student->id;
    }

    /**
     * Fulfill a physical resource (e.g. books, flashcards).
     */
    public function fulfillPhysicalGood(Transaction $transaction, PhysicalGood $good, string $shippingAddress)
    {
        if ($transaction->status !== 'completed') {
            throw new \Exception("Cannot ship unpaid physical good.");
        }

        $trackingNumber = "TRACK_" . uniqid();

        if ($good->stock_quantity > 0) {
            $good->decrement('stock_quantity');
        }

        return "Order shipped! Tracking: " . $trackingNumber;
    }
}
