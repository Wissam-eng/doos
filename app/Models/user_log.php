<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\softDeletes;

class user_log extends Model
{
    use softDeletes;

    protected $fillable = [
        'user_id',
        'action'
    ];

}
