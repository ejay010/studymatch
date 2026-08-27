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

    public function purchasable()
    {
        return $this->morphTo();
    }
}
