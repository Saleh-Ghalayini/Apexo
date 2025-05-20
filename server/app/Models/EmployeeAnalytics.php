<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmployeeAnalytics extends Model
{
    use HasFactory;

    protected $table = 'employee_analytics';

    protected $fillable = [
        'user_id',
        'period_start',
        'period_end',
        'analytics',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'analytics' => 'json',
    ];
}
