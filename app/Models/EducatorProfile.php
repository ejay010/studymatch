<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class EducatorProfile extends Model
{
    use Searchable;

    protected $fillable = [
        'user_id',
        'bio',
        'qualifications',
        'hourly_rate',
        'tier',
        'is_verified',
        'timezone',
    ];

    protected $casts = [
        'qualifications' => 'array',
        'is_verified' => 'boolean',
        'hourly_rate' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function availabilities()
    {
        return $this->hasMany(EducatorAvailability::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function groupClasses()
    {
        return $this->hasMany(GroupClass::class);
    }

    public function digitalResources()
    {
        return $this->hasMany(DigitalResource::class);
    }
}
