<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;

class ParentDashboardController extends Controller
{
    public function index(Request $request)
    {
        // Require the user to be a parent
        abort_unless($request->user()->role === 'parent', 403, 'Unauthorized. Only parents can access this dashboard.');
        
        $user = $request->user();
        $studentProfile = $user->studentProfile;
        $bookings = collect();
        $enrolledClasses = collect();
        $purchasedResources = $user->purchasedResources()->with('educatorProfile.user')->get();

        if ($studentProfile) {
            $bookings = Booking::where('student_profile_id', $studentProfile->id)
                ->where('ends_at', '>=', now())
                ->orderBy('starts_at', 'asc')
                ->with(['educatorProfile.user'])
                ->get();

            $enrolledClasses = $studentProfile->groupClasses()
                ->where('starts_at', '>=', now()->subHours(2))
                ->with(['educatorProfile.user'])
                ->orderBy('starts_at', 'asc')
                ->get();
        }
        
        return view('dashboard.parent', compact('studentProfile', 'bookings', 'enrolledClasses', 'purchasedResources'));
    }
}
