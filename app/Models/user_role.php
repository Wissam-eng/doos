<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\softDeletes;


class user_role extends Model
{
    use HasFactory, softDeletes;
    protected $fillable = ['user_id', 'role_id'];


    public function role()
    {
        return $this->belongsTo(roles::class, 'role_id', 'id');
    }
}
