<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Integration extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'provider',
        'credentials',
        'status',
        'token_type',
        'access_token',
        'refresh_token',
        'expires_at',
        'metadata',
    ];

    protected $hidden = [
        'credentials',
        'access_token',
        'refresh_token',
    ];

    protected function casts(): array
    {
        return [
            'credentials' => 'encrypted:json',
            'metadata' => 'json',
            'expires_at' => 'datetime',
        ];
    }
}
