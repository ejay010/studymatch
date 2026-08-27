<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EducatorDashboardController extends Controller
{
    public function index(Request $request)
    {
        // Require the user to be an educator
        abort_unless($request->user()->role === 'educator', 403, 'Unauthorized. Only educators can access this dashboard.');
        
        $educator = $request->user()->educatorProfile;
        
        return view('dashboard.educator', compact('educator'));
    }
}
