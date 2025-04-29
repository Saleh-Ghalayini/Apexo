<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SlackAnnouncement extends Model
{
    /** @use HasFactory<\Database\Factories\SlackAnnouncementFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'recipient_emails',
        'message',
    ];

    protected $casts = [
        'recipient_emails' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
