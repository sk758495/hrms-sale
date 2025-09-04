<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Laravel\Sanctum\HasApiTokens;

class HR extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens,Notifiable;

    protected $guard = 'hr';
    protected $table = 'hrs';
    
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    protected $fillable = [
        'name',
        'email', 
        'mobile',
        'password',
        'employee_id',
        'is_active',
        'email_verified_at',
        'otp',
        'otp_expires_at'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'otp_expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];
}