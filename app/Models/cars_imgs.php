<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class cars_imgs extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'car_id',
        'img',
    ];
}
