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
}
