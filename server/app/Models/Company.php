<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'domain',
        'description',
        'address',
        'city',
        'state',
        'country',
        'zip_code',
        'phone',
        'website',
        'industry',
        'size',
        'subscription_ends_at',
        'subscription_plan',
        'active',
    ];

    protected function casts(): array
    {
        return [
            'subscription_ends_at' => 'datetime',
            'active' => 'boolean',
            'size' => 'integer',
        ];
    }
}
