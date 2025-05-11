<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Integration extends Model
{
    /** @use HasFactory<\Database\Factories\IntegrationFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
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
}
