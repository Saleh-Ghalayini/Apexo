<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AiPrompt extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'prompt_text',
        'description',
        'category',
        'parameters',
        'is_favorite',
        'is_public',
        'usage_count',
    ];

    protected function casts(): array
    {
        return [
            'intent' => 'json',
            'parameters' => 'json',
            'result' => 'json',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
