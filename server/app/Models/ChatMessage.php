<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ChatMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'chat_session_id',
        'role',
        'content',
        'metadata',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'json',
        ];
    }
}
