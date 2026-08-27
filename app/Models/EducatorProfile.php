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
}
