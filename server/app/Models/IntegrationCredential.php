<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class IntegrationCredential extends Model
{
    use HasFactory;

    protected $fillable = [
        'integration_id',
        'user_id',
        'type',
        'access_token',
        'refresh_token',
        'expires_at',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'expires_at' => 'datetime',
    ];
}
