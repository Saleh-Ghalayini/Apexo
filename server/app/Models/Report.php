<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
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
