<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'user_id',
        'purchasable_id',
        'purchasable_type',
        'amount',
        'commission_amount',
        'status',
        'payment_provider_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'commission_amount' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function purchasable()
    {
        return $this->morphTo();
    }

    public function getEducatorPayoutAttribute(): float
    {
        return (float)max(0, $this->amount - $this->commission_amount);
    }
}
