<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\EducatorProfile;
use App\Models\StudentProfile;
use App\Models\Booking;
use App\Agents\SchedulingAgent;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class BookSession extends Component
{
    public EducatorProfile $educator;
    public string $selectedDate = '';
    public ?string $selectedSlot = null;
    public ?int $selectedStudentId = null;
    public ?string $errorMessage = null;
    public ?string $successMessage = null;
    public ?Booking $confirmedBooking = null;

    public function mount(EducatorProfile $educator)
    {
        $this->educator = $educator;
        // Default to tomorrow to avoid timezone/past-hour issues
        $this->selectedDate = Carbon::tomorrow()->format('Y-m-d');

        if (Auth::check()) {
            $user = Auth::user();
            if ($user->studentProfile) {
                $this->selectedStudentId = $user->studentProfile->id;
            } elseif ($user->role === 'parent') {
                // Ensure parent has a student profile record
                $profile = StudentProfile::firstOrCreate(['user_id' => $user->id]);
                $this->selectedStudentId = $profile->id;
            }
        }
    }

    public function updatedSelectedDate()
    {
        $this->selectedSlot = null;
        $this->errorMessage = null;
    }

    public function selectSlot(string $slotKey)
    {
        $this->selectedSlot = $slotKey;
        $this->errorMessage = null;
    }

    public function book()
    {
        $this->errorMessage = null;
        $this->successMessage = null;

        if (!Auth::check()) {
            return redirect()->guest(route('login'));
        }

        $user = Auth::user();

        if ($user->role === 'educator') {
            $this->errorMessage = "Educator accounts cannot book tutoring sessions. Please use a parent account.";
            return;
        }

        if (!$this->selectedSlot) {
            $this->errorMessage = "Please select an available time slot.";
            return;
        }

        // Ensure student profile
        $studentProfile = $user->studentProfile ?: StudentProfile::firstOrCreate(['user_id' => $user->id]);
        $this->selectedStudentId = $studentProfile->id;

        $parts = explode('|', $this->selectedSlot);
        if (count($parts) !== 2) {
            $this->errorMessage = "Invalid time slot selected.";
            return;
        }

        [$startsAt, $endsAt] = $parts;

        try {
            $agent = new SchedulingAgent();
            $this->confirmedBooking = $agent->schedule1on1(
                educatorId: $this->educator->id,
                studentId: $this->selectedStudentId,
                startsAt: $startsAt,
                endsAt: $endsAt
            );

            $this->successMessage = "Session booked successfully!";
            $this->selectedSlot = null;
        } catch (\Exception $e) {
            $this->errorMessage = $e->getMessage();
        }
    }

    public function render()
    {
        $slots = [];
        if (!empty($this->selectedDate)) {
            $agent = new SchedulingAgent();
            $slots = $agent->getAvailableSlots($this->educator, $this->selectedDate);
        }

        return view('livewire.book-session', [
            'slots' => $slots,
        ]);
    }
}
