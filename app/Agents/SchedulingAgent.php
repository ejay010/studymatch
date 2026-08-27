<?php

namespace App\Agents;

use App\Models\Booking;
use App\Models\EducatorProfile;

class SchedulingAgent
{
    /**
     * Attempt to schedule a booking without conflicts.
     * 
     * @param int $educatorId
     * @param int $studentId
     * @param string $startsAt
     * @param string $endsAt
     * @return Booking
     * @throws \Exception
     */
    public function schedule1on1(int $educatorId, int $studentId, string $startsAt, string $endsAt)
    {
        $educator = EducatorProfile::findOrFail($educatorId);

        // Check for conflicts
        $conflicts = Booking::where('educator_profile_id', $educator->id)
            ->where(function ($query) use ($startsAt, $endsAt) {
                $query->whereBetween('starts_at', [$startsAt, $endsAt])
                      ->orWhereBetween('ends_at', [$startsAt, $endsAt]);
            })
            ->exists();

        if ($conflicts) {
            throw new \Exception("The educator is already booked for this time slot.");
        }

        // Create the booking
        $booking = Booking::create([
            'educator_profile_id' => $educator->id,
            'student_profile_id' => $studentId,
            'type' => '1-on-1',
            'status' => 'confirmed',
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
            'max_capacity' => 1,
        ]);

        // Here we would dispatch a queued job to generate meeting links via the CalendarIntegrationService

        return $booking;
    }
}
