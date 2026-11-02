<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentProfile extends Model
{
    protected $fillable = [
        'user_id',
        'grade_level',
        'subjects_of_interest',
        'learning_styles',
        'is_premium',
    ];

    protected $casts = [
        'subjects_of_interest' => 'array',
        'learning_styles' => 'array',
        'grade_level' => 'integer',
        'is_premium' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function enrollments()
    {
        return $this->hasMany(ClassEnrollment::class);
    }

    public function groupClasses()
    {
        return $this->belongsToMany(GroupClass::class, 'class_enrollments');
    }
}
