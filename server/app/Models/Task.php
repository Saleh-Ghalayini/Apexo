<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'assignee_id',
        'title',
        'description',
        'deadline',
        'status',
        'priority',
        'external_id',
        'external_url',
        'external_system',
        'external_data',
    ];

    protected function casts(): array
    {
        return [
            'deadline' => 'datetime',
            'external_data' => 'json',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }

    public function source()
    {
        return $this->morphTo();
    }
}
