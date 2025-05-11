<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IntegrationData extends Model
{
    /** @use HasFactory<\Database\Factories\IntegrationDataFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
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
