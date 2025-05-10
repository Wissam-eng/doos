<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class earnings extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'doos',
        'car_owner',
        'driver',
        'insurance_amount',
        'insurance_status',
        'order_id'
    ];

    public function order()
    {
        return $this->belongsTo(orders_cars::class);
    }
}
