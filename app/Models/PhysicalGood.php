<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhysicalGood extends Model
{
    protected $fillable = [
        'educator_profile_id',
        'name',
        'description',
        'price',
        'shipping_cost',
        'stock_quantity',
    ];

    public function educatorProfile()
    {
        return $this->belongsTo(EducatorProfile::class);
    }
}
