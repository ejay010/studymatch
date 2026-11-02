<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupClass extends Model
{
    protected $fillable = [
        'educator_profile_id',
        'title',
        'description',
        'subject',
        'grade_level',
        'price',
        'starts_at',
        'duration_minutes',
        'max_capacity',
        'meeting_link',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'price' => 'decimal:2',
        'grade_level' => 'integer',
        'duration_minutes' => 'integer',
        'max_capacity' => 'integer',
    ];

    public function educatorProfile()
    {
        return $this->belongsTo(EducatorProfile::class);
    }

    public function enrollments()
    {
        return $this->hasMany(ClassEnrollment::class);
    }

    public function students()
    {
        return $this->belongsToMany(StudentProfile::class, 'class_enrollments');
    }

    public function getEnrolledCountAttribute(): int
    {
        return $this->enrollments()->where('status', 'confirmed')->count();
    }

    public function getRemainingSpotsAttribute(): int
    {
        return max(0, $this->max_capacity - $this->enrolled_count);
    }

    public function getIsFullAttribute(): bool
    {
        return $this->remaining_spots <= 0;
    }
}
