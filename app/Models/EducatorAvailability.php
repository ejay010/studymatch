<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EducatorAvailability extends Model
{
    protected $fillable = [
        'educator_profile_id',
        'day_of_week',
        'start_time',
        'end_time',
        'is_available',
    ];

    protected $casts = [
        'day_of_week' => 'integer',
        'is_available' => 'boolean',
    ];

    public function educatorProfile()
    {
        return $this->belongsTo(EducatorProfile::class);
    }

    public static function dayNames(): array
    {
        return [
            0 => 'Sunday',
            1 => 'Monday',
            2 => 'Tuesday',
            3 => 'Wednesday',
            4 => 'Thursday',
            5 => 'Friday',
            6 => 'Saturday',
        ];
    }

    public function getDayNameAttribute(): string
    {
        return self::dayNames()[$this->day_of_week] ?? 'Unknown';
    }
}
