<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\StudentProfile;
use Illuminate\Support\Facades\Auth;

class StudentProfileForm extends Component
{
    public $grade_level = '';
    public $subjects_of_interest = '';
    public $learning_styles = '';
    public bool $isSaved = false;

    public function mount()
    {
        $profile = Auth::user()->studentProfile;
        
        if ($profile) {
            $this->grade_level = $profile->grade_level;
            $this->subjects_of_interest = $profile->subjects_of_interest ? implode(', ', $profile->subjects_of_interest) : '';
            $this->learning_styles = $profile->learning_styles ? implode(', ', $profile->learning_styles) : '';
        }
    }

    public function rules()
    {
        return [
            'grade_level' => 'nullable|integer|min:1|max:12',
            'subjects_of_interest' => 'nullable|string|max:500',
            'learning_styles' => 'nullable|string|max:500',
        ];
    }

    public function save()
    {
        $this->validate();

        $subjects = array_filter(array_map('trim', explode(',', $this->subjects_of_interest)));
        $styles = array_filter(array_map('trim', explode(',', $this->learning_styles)));

        StudentProfile::updateOrCreate(
            ['user_id' => Auth::id()],
            [
                'grade_level' => $this->grade_level ?: null,
                'subjects_of_interest' => empty($subjects) ? null : $subjects,
                'learning_styles' => empty($styles) ? null : $styles,
            ]
        );

        $this->isSaved = true;

        // Refresh page to update dashboard
        return redirect()->route('dashboard.parent');
    }

    public function render()
    {
        return view('livewire.student-profile-form');
    }
}
