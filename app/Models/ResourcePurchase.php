<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResourcePurchase extends Model
{
    protected $fillable = [
        'digital_resource_id',
        'user_id',
        'price_paid',
    ];

    protected $casts = [
        'price_paid' => 'decimal:2',
    ];

    public function digitalResource()
    {
        return $this->belongsTo(DigitalResource::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
