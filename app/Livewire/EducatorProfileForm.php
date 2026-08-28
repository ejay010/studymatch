<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\EducatorProfile;
use Illuminate\Support\Facades\Auth;

class EducatorProfileForm extends Component
{
    public $bio = '';
    public $hourly_rate = 30.00;
    public $timezone = 'America/New_York';
    public $qualifications = '';

    public bool $isSaved = false;

    public function mount()
    {
        $profile = Auth::user()->educatorProfile;
        
        if ($profile) {
            $this->bio = $profile->bio;
            $this->hourly_rate = $profile->hourly_rate;
            $this->timezone = $profile->timezone ?? 'America/New_York';
            $this->qualifications = $profile->qualifications ? implode("\n", $profile->qualifications) : '';
        }
    }

    public function rules()
    {
        return [
            'bio' => 'required|string|min:10|max:1000',
            'hourly_rate' => 'required|numeric|min:5|max:500',
            'timezone' => 'required|string',
            'qualifications' => 'nullable|string|max:1000',
        ];
    }

    public function save()
    {
        $this->validate();

        $qualificationsArray = array_filter(array_map('trim', explode("\n", $this->qualifications)));

        EducatorProfile::updateOrCreate(
            ['user_id' => Auth::id()],
            [
                'bio' => $this->bio,
                'hourly_rate' => $this->hourly_rate,
                'timezone' => $this->timezone,
                'qualifications' => $qualificationsArray,
            ]
        );

        $this->isSaved = true;

        // Force a page refresh to update the dashboard UI
        return redirect()->route('dashboard.educator');
    }

    public function render()
    {
        return view('livewire.educator-profile-form');
    }
}
