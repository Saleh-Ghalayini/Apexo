<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meeting extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
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
    ];
}
