<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'company_id',
        'type',
        'title',
        'description',
        'parameters',
        'data',
        'period_start',
        'period_end',
        'is_scheduled',
        'schedule',
        'last_generated_at',
    ];
}
