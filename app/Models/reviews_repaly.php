<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class reviews_repaly extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'review_id',
        'driver_id',
        'doos_user_id',
        'love',
        'comment',
    ];
}
