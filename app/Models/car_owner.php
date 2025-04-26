<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\SoftDeletes;



class car_owner extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable, SoftDeletes;



    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }


    protected $fillable = [
        'email',
        'phone',
        'otp',
        'email_verified_at',
        'img',
        'role',
        'status',
        'password',


        'membership_id',


        'legal_name',
        'employee_id_number',
        'vat_number',
        'head_office_address',


        'first_name',
        'last_name',
        'date_of_birth',
        'company_id',
        'address',


        'address2',
        'zip_code',
        'city',
        'country',

        'notice_before_trip',
        'min_duration_trip',
        'max_duration_trip',
    ];


    protected $hidden = [
        'otp',
        'password',
        'remember_token',
    ];


    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }


    public function cars()
    {
        return $this->hasMany(cars::class, 'car_owner_id', 'id');
    }
}
