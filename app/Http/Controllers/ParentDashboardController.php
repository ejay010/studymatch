<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ParentDashboardController extends Controller
{
    public function index(Request $request)
    {
        // Require the user to be a parent
        abort_unless($request->user()->role === 'parent', 403, 'Unauthorized. Only parents can access this dashboard.');
        
        $studentProfile = $request->user()->studentProfile;
        
        return view('dashboard.parent', compact('studentProfile'));
    }
}
