<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'company_id',
        'type',
        'title',
        'description',
        'parameters',
        'data',
        'period_start',
        'period_end',
        'is_scheduled',
        'schedule',
        'last_generated_at',
    ];

    protected function casts(): array
    {
        return [
            'parameters' => 'json',
            'data' => 'json',
            'period_start' => 'datetime',
            'period_end' => 'datetime',
            'is_scheduled' => 'boolean',
            'last_generated_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
