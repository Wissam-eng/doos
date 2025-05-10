<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;



class roles extends Model
{
    use SoftDeletes;
    
    protected $fillable =[
        'role',
        'membership',
        'repaly_review',
        'users_mangement',
        'financial',
        'rental',
        'permissions',
        'car_owners',
        'car_renters',
        'drivers',
        'status',
    ];
}
