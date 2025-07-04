<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Permissions extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'role_id',
        'permission',
        'add',
        'edit',
        'view',
        'status',
        'delete'
    ];
}
