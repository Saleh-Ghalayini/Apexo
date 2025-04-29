<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatSession extends Model
{
    /** @use HasFactory<\Database\Factories\ChatSessionFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'summary',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
