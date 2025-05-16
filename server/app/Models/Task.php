<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    /** @use HasFactory<\Database\Factories\TaskFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
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

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'deadline' => 'datetime',
            'external_data' => 'json',
        ];
    }

    /**
     * Get the user that created the task.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }

    /**
     * Get the source model (meeting or prompt) that generated this task.
     */
    public function source()
    {
        return $this->morphTo();
    }
}
