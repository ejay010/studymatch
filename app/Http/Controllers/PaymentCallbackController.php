<?php

namespace App\Http\Controllers;

use App\Agents\CommerceAgent;
use Illuminate\Http\Request;

class PaymentCallbackController extends Controller
{
    public function handleReturn(Request $request)
    {
        $orderId = $request->query('token') ?? $request->query('orderId');

        if (!$orderId) {
            return redirect()->route('home')->with('error', 'Missing payment token from PayPal.');
        }

        try {
            $agent = new CommerceAgent();
            $transaction = $agent->captureOrder($orderId);

            return redirect()->route('dashboard.parent')->with('status', 'Payment successful! Your order has been confirmed.');
        } catch (\Exception $e) {
            return redirect()->route('home')->with('error', 'Payment failed: ' . $e->getMessage());
        }
    }

    public function handleCancel(Request $request)
    {
        return redirect()->route('home')->with('info', 'Payment was cancelled.');
    }
}
