<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    /** @use HasFactory<\Database\Factories\CompanyFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'domain',
        'logo',
        'description',
        'address',
        'city',
        'state',
        'country',
        'zip_code',
        'phone',
        'website',
        'industry',
        'size',
        'subscription_ends_at',
        'subscription_plan',
        'active',
    ];
}
