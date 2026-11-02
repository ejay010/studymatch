<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\GroupClass;
use App\Models\StudentProfile;
use App\Models\ClassEnrollment;
use App\Agents\SchedulingAgent;
use Illuminate\Support\Facades\Auth;

class GroupClassCatalog extends Component
{
    public string $search = '';
    public string $selectedSubject = '';
    public string $selectedGrade = '';

    public ?string $enrollmentSuccess = null;
    public ?string $enrollmentError = null;

    public function enroll(int $classId)
    {
        $this->enrollmentSuccess = null;
        $this->enrollmentError = null;

        if (!Auth::check()) {
            return redirect()->guest(route('login'));
        }

        $user = Auth::user();

        if ($user->role === 'educator') {
            $this->enrollmentError = "Educator accounts cannot enroll in classes. Please log in as a parent.";
            return;
        }

        $studentProfile = $user->studentProfile ?: StudentProfile::firstOrCreate(['user_id' => $user->id]);

        try {
            $agent = new SchedulingAgent();
            $enrollment = $agent->enrollInGroupClass(
                groupClassId: $classId,
                studentProfileId: $studentProfile->id,
                userId: $user->id
            );

            $this->enrollmentSuccess = "Successfully enrolled in " . $enrollment->groupClass->title . "! You can view class details in your dashboard.";
        } catch (\Exception $e) {
            $this->enrollmentError = $e->getMessage();
        }
    }

    public function render()
    {
        $query = GroupClass::with(['educatorProfile.user', 'enrollments'])
            ->where('starts_at', '>=', now())
            ->orderBy('starts_at', 'asc');

        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%')
                  ->orWhere('subject', 'like', '%' . $this->search . '%');
            });
        }

        if (!empty($this->selectedSubject)) {
            $query->where('subject', $this->selectedSubject);
        }

        if (!empty($this->selectedGrade)) {
            $query->where('grade_level', (int)$this->selectedGrade);
        }

        $classes = $query->get();

        // Check which classes currently logged in parent has already enrolled in
        $userEnrolledClassIds = [];
        if (Auth::check() && Auth::user()->studentProfile) {
            $userEnrolledClassIds = ClassEnrollment::where('student_profile_id', Auth::user()->studentProfile->id)
                ->pluck('group_class_id')
                ->toArray();
        }

        $subjects = ['Math', 'Science', 'English & Reading', 'Coding & Tech', 'History & Social Studies', 'Art & Music'];

        return view('livewire.group-class-catalog', [
            'classes' => $classes,
            'subjects' => $subjects,
            'userEnrolledClassIds' => $userEnrolledClassIds,
        ]);
    }
}
