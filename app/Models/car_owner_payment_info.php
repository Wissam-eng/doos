<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class car_owner_payment_info extends Model
{
    use SoftDeletes;


    protected $fillable = [
        'car_owner_id',
        'name_on_card',
        'card_number',
        'expiration_date',
        'cvv',
        'country',
        'address_line_1',
        'address_line_2',
        'city',
        'zip_code',
    ];
}
