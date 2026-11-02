<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\GroupClass;
use App\Models\DigitalResource;
use App\Models\Transaction;
use Illuminate\Http\Request;

class EducatorDashboardController extends Controller
{
    public function index(Request $request)
    {
        // Require the user to be an educator
        abort_unless($request->user()->role === 'educator', 403, 'Unauthorized. Only educators can access this dashboard.');
        
        $educator = $request->user()->educatorProfile;
        $bookings = collect();
        $monthlyEarnings = 0.00;
        $transactions = collect();

        if ($educator) {
            $bookings = Booking::where('educator_profile_id', $educator->id)
                ->where('ends_at', '>=', now())
                ->orderBy('starts_at', 'asc')
                ->with(['studentProfile.user'])
                ->get();

            $bookingIds = $educator->bookings()->pluck('id');
            $groupClassIds = $educator->groupClasses()->pluck('id');
            $resourceIds = $educator->digitalResources()->pluck('id');

            $transactions = Transaction::where('status', 'completed')
                ->where(function ($q) use ($bookingIds, $groupClassIds, $resourceIds) {
                    $q->where(function ($sub) use ($bookingIds) {
                        $sub->where('purchasable_type', Booking::class)->whereIn('purchasable_id', $bookingIds);
                    })->orWhere(function ($sub) use ($groupClassIds) {
                        $sub->where('purchasable_type', GroupClass::class)->whereIn('purchasable_id', $groupClassIds);
                    })->orWhere(function ($sub) use ($resourceIds) {
                        $sub->where('purchasable_type', DigitalResource::class)->whereIn('purchasable_id', $resourceIds);
                    });
                })
                ->orderBy('created_at', 'desc')
                ->get();

            $monthlyEarnings = $transactions->sum(function ($tx) {
                return max(0, $tx->amount - $tx->commission_amount);
            });
        }
        
        return view('dashboard.educator', compact('educator', 'bookings', 'monthlyEarnings', 'transactions'));
    }
}
