<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Integration extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'name',
        'email',
        'provider',
        'status',
        'settings',
        'connected_at',
        'disconnected_at',
    ];

    protected $casts = [
        'settings' => 'array',
        'connected_at' => 'datetime',
        'disconnected_at' => 'datetime'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
