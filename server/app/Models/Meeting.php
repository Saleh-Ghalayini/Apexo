<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meeting extends Model
{
    /** @use HasFactory<\Database\Factories\MeetingFactory> */
    use HasFactory;

    protected $fillable = [
        'company_id',
        'transcript',
        'summary',
        'scheduled_at',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
