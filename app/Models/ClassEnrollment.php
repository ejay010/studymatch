<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassEnrollment extends Model
{
    protected $fillable = [
        'group_class_id',
        'student_profile_id',
        'user_id',
        'status',
    ];

    public function groupClass()
    {
        return $this->belongsTo(GroupClass::class);
    }

    public function studentProfile()
    {
        return $this->belongsTo(StudentProfile::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
