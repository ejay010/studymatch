<?php

namespace App\Agents;

use App\Models\Booking;
use App\Models\EducatorProfile;
use App\Models\EducatorAvailability;
use App\Models\GroupClass;
use App\Models\ClassEnrollment;
use App\Services\CalendarIntegrationService;
use Carbon\Carbon;

class SchedulingAgent
{
    /**
     * Get available 1-on-1 time slots for an educator on a specific date.
     *
     * @param EducatorProfile $educator
     * @param string $dateString Format: 'Y-m-d'
     * @param int $slotDurationMinutes
     * @return array
     */
    public function getAvailableSlots(EducatorProfile $educator, string $dateString, int $slotDurationMinutes = 60): array
    {
        $timezone = $educator->timezone ?: config('app.timezone', 'America/New_York');
        $date = Carbon::parse($dateString, $timezone);
        $dayOfWeek = $date->dayOfWeek; // 0 (Sunday) to 6 (Saturday)

        // Check configured availability
        $availability = EducatorAvailability::where('educator_profile_id', $educator->id)
            ->where('day_of_week', $dayOfWeek)
            ->first();

        // If educator has set no custom availabilities yet, provide default Mon-Fri 09:00 - 17:00
        $hasCustomAvailability = EducatorAvailability::where('educator_profile_id', $educator->id)->exists();

        if (!$availability) {
            if ($hasCustomAvailability || in_array($dayOfWeek, [0, 6])) {
                return []; // Weekend or day is off
            }
            $startTimeStr = '09:00:00';
            $endTimeStr = '17:00:00';
        } else {
            if (!$availability->is_available) {
                return [];
            }
            $startTimeStr = $availability->start_time;
            $endTimeStr = $availability->end_time;
        }

        $dayStart = Carbon::parse($dateString . ' ' . $startTimeStr, $timezone);
        $dayEnd = Carbon::parse($dateString . ' ' . $endTimeStr, $timezone);

        // Fetch existing bookings for this day
        $existingBookings = Booking::where('educator_profile_id', $educator->id)
            ->where('status', '!=', 'cancelled')
            ->where('starts_at', '<', $dayEnd)
            ->where('ends_at', '>', $dayStart)
            ->get();

        $slots = [];
        $currentSlotStart = $dayStart->copy();

        while ($currentSlotStart->copy()->addMinutes($slotDurationMinutes)->lte($dayEnd)) {
            $currentSlotEnd = $currentSlotStart->copy()->addMinutes($slotDurationMinutes);

            // Check if slot overlaps with an existing booking
            $hasConflict = $existingBookings->contains(function ($booking) use ($currentSlotStart, $currentSlotEnd) {
                return $booking->starts_at < $currentSlotEnd && $booking->ends_at > $currentSlotStart;
            });

            // Check if slot is already in the past
            $isPast = $currentSlotStart->copy()->setTimezone(config('app.timezone'))->isPast();

            $slots[] = [
                'starts_at' => $currentSlotStart->format('Y-m-d H:i:s'),
                'ends_at' => $currentSlotEnd->format('Y-m-d H:i:s'),
                'formatted_time' => $currentSlotStart->format('g:i A') . ' - ' . $currentSlotEnd->format('g:i A'),
                'is_available' => !$hasConflict && !$isPast,
            ];

            $currentSlotStart->addMinutes($slotDurationMinutes);
        }

        return $slots;
    }

    /**
     * Attempt to schedule a 1-on-1 booking without conflicts.
     * 
     * @param int $educatorId
     * @param int $studentId
     * @param string $startsAt
     * @param string $endsAt
     * @return Booking
     * @throws \Exception
     */
    public function schedule1on1(int $educatorId, int $studentId, string $startsAt, string $endsAt): Booking
    {
        $educator = EducatorProfile::findOrFail($educatorId);

        $parsedStartsAt = Carbon::parse($startsAt);
        $parsedEndsAt = Carbon::parse($endsAt);

        if ($parsedStartsAt->gte($parsedEndsAt)) {
            throw new \Exception("Session start time must be before end time.");
        }

        // Check for conflicts
        $conflict = Booking::where('educator_profile_id', $educator->id)
            ->where('status', '!=', 'cancelled')
            ->where(function ($query) use ($parsedStartsAt, $parsedEndsAt) {
                $query->where('starts_at', '<', $parsedEndsAt)
                      ->where('ends_at', '>', $parsedStartsAt);
            })
            ->exists();

        if ($conflict) {
            throw new \Exception("The educator is already booked for this time slot.");
        }

        // Generate meeting link
        $calendarService = new CalendarIntegrationService();
        $meetingLink = $calendarService->generateMeetingLink($educator, null);

        // Create the booking
        return Booking::create([
            'educator_profile_id' => $educator->id,
            'student_profile_id' => $studentId,
            'type' => '1-on-1',
            'status' => 'confirmed',
            'starts_at' => $parsedStartsAt,
            'ends_at' => $parsedEndsAt,
            'meeting_link' => $meetingLink,
            'max_capacity' => 1,
        ]);
    }

    /**
     * Enroll a student in a small-group class while strictly enforcing the capacity cap.
     *
     * @param int $groupClassId
     * @param int $studentProfileId
     * @param int $userId
     * @return ClassEnrollment
     * @throws \Exception
     */
    public function enrollInGroupClass(int $groupClassId, int $studentProfileId, int $userId): ClassEnrollment
    {
        $class = GroupClass::findOrFail($groupClassId);

        // Check if already enrolled
        $alreadyEnrolled = ClassEnrollment::where('group_class_id', $class->id)
            ->where('student_profile_id', $studentProfileId)
            ->exists();

        if ($alreadyEnrolled) {
            throw new \Exception("This student is already enrolled in this class.");
        }

        // Check capacity cap
        $currentEnrolled = ClassEnrollment::where('group_class_id', $class->id)
            ->where('status', 'confirmed')
            ->count();

        if ($currentEnrolled >= $class->max_capacity) {
            throw new \Exception("This class is full. Maximum capacity of {$class->max_capacity} students reached.");
        }

        return ClassEnrollment::create([
            'group_class_id' => $class->id,
            'student_profile_id' => $studentProfileId,
            'user_id' => $userId,
            'status' => 'confirmed',
        ]);
    }
}
