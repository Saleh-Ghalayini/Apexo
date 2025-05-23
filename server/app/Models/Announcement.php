<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Announcement extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'message',
        'slack_channel',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
