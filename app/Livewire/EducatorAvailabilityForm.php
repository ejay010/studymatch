<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\EducatorAvailability;
use Illuminate\Support\Facades\Auth;

class EducatorAvailabilityForm extends Component
{
    public array $days = [];
    public bool $isSaved = false;

    public function mount()
    {
        $educator = Auth::user()->educatorProfile;

        $existing = $educator ? $educator->availabilities->keyBy('day_of_week') : collect();

        // Days ordered from Monday (1) to Sunday (0)
        $orderedDays = [1, 2, 3, 4, 5, 6, 0];

        foreach ($orderedDays as $day) {
            if ($existing->has($day)) {
                $rec = $existing->get($day);
                $this->days[$day] = [
                    'is_available' => (bool)$rec->is_available,
                    'start_time' => substr($rec->start_time, 0, 5),
                    'end_time' => substr($rec->end_time, 0, 5),
                ];
            } else {
                // Default: weekdays 09:00 - 17:00 available, weekends off
                $isWeekday = in_array($day, [1, 2, 3, 4, 5]);
                $this->days[$day] = [
                    'is_available' => $isWeekday,
                    'start_time' => '09:00',
                    'end_time' => '17:00',
                ];
            }
        }
    }

    public function rules()
    {
        return [
            'days.*.is_available' => 'boolean',
            'days.*.start_time' => 'required|date_format:H:i',
            'days.*.end_time' => 'required|date_format:H:i',
        ];
    }

    public function save()
    {
        $this->validate();

        $educator = Auth::user()->educatorProfile;

        if (!$educator) {
            return;
        }

        foreach ($this->days as $day => $settings) {
            EducatorAvailability::updateOrCreate(
                [
                    'educator_profile_id' => $educator->id,
                    'day_of_week' => (int)$day,
                ],
                [
                    'is_available' => (bool)$settings['is_available'],
                    'start_time' => $settings['start_time'] . ':00',
                    'end_time' => $settings['end_time'] . ':00',
                ]
            );
        }

        $this->isSaved = true;
    }

    public function render()
    {
        $dayNames = EducatorAvailability::dayNames();
        return view('livewire.educator-availability-form', compact('dayNames'));
    }
}
