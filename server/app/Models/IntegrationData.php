<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class IntegrationData extends Model
{
    use HasFactory;

    protected $fillable = [
        'integration_id',
        'data_type',
        'external_id',
        'name',
        'description',
        'data',
        'is_active',
    ];
}
