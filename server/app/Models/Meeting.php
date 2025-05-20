<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Meeting extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'scheduled_at',
        'ended_at',
        'transcript',
        'summary',
        'status',
        'external_id',
        'meeting_url',
        'attendees',
        'metadata',
        'analytics',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'ended_at' => 'datetime',
            'attendees' => 'json',
            'metadata' => 'json',
            'analytics' => 'json',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tasks()
    {
        return $this->morphMany(Task::class, 'source');
    }
}
