<?php

namespace App\Http\Controllers;

use App\Models\DigitalResource;
use App\Models\ResourcePurchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DigitalResourceDownloadController extends Controller
{
    public function download(DigitalResource $resource)
    {
        $user = Auth::user();

        // 1. Check if resource is free
        if ($resource->is_free) {
            return $this->deliverFile($resource);
        }

        // 2. Paid resources require authentication
        if (!$user) {
            return redirect()->guest(route('login'));
        }

        // 3. Check if the authenticated user is the educator who created it
        if ($user->educatorProfile && $user->educatorProfile->id === $resource->educator_profile_id) {
            return $this->deliverFile($resource);
        }

        // 4. Check if the user is a Premium subscriber (per AGENTS.md)
        if ($user->studentProfile && $user->studentProfile->is_premium) {
            return $this->deliverFile($resource);
        }

        // 5. Check if the user has purchased the resource
        $hasPurchased = ResourcePurchase::where('digital_resource_id', $resource->id)
            ->where('user_id', $user->id)
            ->exists();

        if ($hasPurchased) {
            return $this->deliverFile($resource);
        }

        abort(403, 'You must purchase this resource before downloading it.');
    }

    private function deliverFile(DigitalResource $resource)
    {
        $resource->increment('downloads_count');

        if (!Storage::disk('local')->exists($resource->file_path)) {
            abort(404, 'The requested file could not be found on storage.');
        }

        return Storage::disk('local')->download($resource->file_path, $resource->file_name);
    }
}
