<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class reviews extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'renter_id',
        'order_id',
        'rate',
        'comment',
    ];

    public function order(){
        return $this->belongsTo(orders_cars::class);
    }

    public function repaly(){
        return $this->hasMany(reviews_repaly::class , 'review_id', 'id');
    }
}
