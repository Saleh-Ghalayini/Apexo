<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    /** @use HasFactory<\Database\Factories\ReportFactory> */
    use HasFactory;

    protected $fillable = [
        'company_id',
        'type',
        'data',
        'generated_at',
    ];

    protected $casts = [
        'data' => 'array'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
