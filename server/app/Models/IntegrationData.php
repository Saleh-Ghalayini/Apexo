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

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'data' => 'json',
            'is_active' => 'boolean',
        ];
    }

    public function integration()
    {
        return $this->belongsTo(Integration::class);
    }
}
