<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EducatorProfile;

class EducatorProfileController extends Controller
{
    public function show($id)
    {
        $educator = EducatorProfile::with('user')->findOrFail($id);
        
        return view('educator.show', compact('educator'));
    }
}
