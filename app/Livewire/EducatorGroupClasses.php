<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\GroupClass;
use App\Services\CalendarIntegrationService;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class EducatorGroupClasses extends Component
{
    public bool $showCreateForm = false;
    public ?int $selectedClassRosterId = null;

    // Form fields
    public string $title = '';
    public string $description = '';
    public string $subject = 'Math';
    public ?int $grade_level = 5;
    public $price = 25.00;
    public string $starts_at = '';
    public int $duration_minutes = 60;
    public int $max_capacity = 6;

    public ?string $successMessage = null;

    public function mount()
    {
        $this->starts_at = Carbon::now()->addDays(3)->setHour(10)->setMinute(0)->format('Y-m-d\TH:i');
    }

    public function rules()
    {
        return [
            'title' => 'required|string|min:3|max:200',
            'description' => 'nullable|string|max:1000',
            'subject' => 'required|string|max:100',
            'grade_level' => 'nullable|integer|min:1|max:12',
            'price' => 'required|numeric|min:0|max:500',
            'starts_at' => 'required|date|after:now',
            'duration_minutes' => 'required|integer|min:15|max:180',
            'max_capacity' => 'required|integer|min:2|max:30',
        ];
    }

    public function save()
    {
        $this->validate();

        $educator = Auth::user()->educatorProfile;
        if (!$educator) {
            return;
        }

        $calendarService = new CalendarIntegrationService();
        $meetingLink = $calendarService->generateMeetingLink($educator, null);

        GroupClass::create([
            'educator_profile_id' => $educator->id,
            'title' => $this->title,
            'description' => $this->description,
            'subject' => $this->subject,
            'grade_level' => $this->grade_level ?: null,
            'price' => $this->price,
            'starts_at' => Carbon::parse($this->starts_at),
            'duration_minutes' => $this->duration_minutes,
            'max_capacity' => $this->max_capacity,
            'meeting_link' => $meetingLink,
        ]);

        $this->reset(['title', 'description', 'price']);
        $this->showCreateForm = false;
        $this->successMessage = "Small group class created successfully!";
    }

    public function toggleRoster(int $classId)
    {
        $this->selectedClassRosterId = ($this->selectedClassRosterId === $classId) ? null : $classId;
    }

    public function render()
    {
        $educator = Auth::user()->educatorProfile;
        $classes = $educator 
            ? GroupClass::where('educator_profile_id', $educator->id)
                ->with(['enrollments.studentProfile.user'])
                ->orderBy('starts_at', 'desc')
                ->get()
            : collect();

        return view('livewire.educator-group-classes', [
            'classes' => $classes,
        ]);
    }
}
