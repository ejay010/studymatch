<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'educator_profile_id',
        'student_profile_id',
        'type',
        'status',
        'starts_at',
        'ends_at',
        'meeting_link',
        'max_capacity',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'max_capacity' => 'integer',
    ];
}
