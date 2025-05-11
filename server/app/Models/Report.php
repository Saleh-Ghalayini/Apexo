<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
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

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
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
}
