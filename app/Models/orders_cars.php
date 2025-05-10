<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class orders_cars extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'car_id',
        'renter_id',

        'latitude_from',
        'longitude_from',
        'latitude_to',
        'longitude_to',

        'distance',

        'min_price',
        'max_price',
        'price',

        'check_before',
        'check_after',

        'status',

        'actual_latitude_from',
        'actual_longitude_from',
        'actual_latitude_to',
        'actual_longitude_to',
        'actual_distance',

        'extra_distance',

        'driver_id',
        'driver',

        'Insurance_amount_for_trip',
        'Insurance_amount_for_car',

        'contract_file',
    ];


    public function car()
    {
        return $this->belongsTo(cars::class, 'car_id', 'id');
    }
}
