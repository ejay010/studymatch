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
    ];
}
