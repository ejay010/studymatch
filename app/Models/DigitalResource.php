<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DigitalResource extends Model
{
    protected $fillable = [
        'educator_profile_id',
        'title',
        'description',
        'subject',
        'grade_level',
        'price',
        'file_path',
        'file_name',
        'file_size',
        'file_type',
        'downloads_count',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'grade_level' => 'integer',
        'file_size' => 'integer',
        'downloads_count' => 'integer',
    ];

    public function educatorProfile()
    {
        return $this->belongsTo(EducatorProfile::class);
    }

    public function purchases()
    {
        return $this->hasMany(ResourcePurchase::class);
    }

    public function buyers()
    {
        return $this->belongsToMany(User::class, 'resource_purchases');
    }

    public function getIsFreeAttribute(): bool
    {
        return $this->price <= 0.00;
    }

    public function getFormattedSizeAttribute(): string
    {
        $bytes = $this->file_size;
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 1) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 0) . ' KB';
        }
        return $bytes . ' B';
    }
}
