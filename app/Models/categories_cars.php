<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class categories_cars extends Model
{
    use SoftDeletes;

    protected $table = 'categories_cars';

    protected $fillable = [

        'make',
        'model',
        'year',
        'category',
        'seats',
        'transmission',
        'fuel_type',
        'engine_capacity',
        'logo',
        'description',

    ];


    public function car()
    {

        return $this->hasMany(cars::class, 'categories_cars_id', 'id');
    }
}
