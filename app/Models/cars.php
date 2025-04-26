<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class cars extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'car_owner_id',
        'categories_cars_id',
        'car_location',
        'car_vin',
        'car_model',
        'car_mileage_range',
        'transmission',
        'mechanical_condition',
        'all_seats_seatable',
        'additional_info',
        'number_of_door',
        'number_of_seats',
        'features',
        'description',
        'license_plate_number',
        'state'
    ];

    public function imgs()
    {
        return $this->hasMany(cars_imgs::class, 'car_id', 'id');
    }
}
