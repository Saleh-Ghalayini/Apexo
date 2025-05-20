<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'company_id',
        'name',
        'email',
        'password',
        'role',
        'job_title',
        'department',
        'phone',
        'avatar',
        'active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'active' => 'boolean',
            'google_calendar_token' => 'array',
        ];
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [
            'company_id' => $this->company_id,
            'role' => $this->role,
        ];
    }

    public function isEmployee()
    {
        return $this->role === 'employee';
    }

    public function isManager()
    {
        return $this->role === 'manager';
    }

    public function isHR()
    {
        return $this->role === 'hr';
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
