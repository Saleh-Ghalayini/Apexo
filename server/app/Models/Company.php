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

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'subscription_ends_at' => 'datetime',
            'active' => 'boolean',
            'size' => 'integer',
        ];
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function meetings()
    {
        return $this->hasManyThrough(Meeting::class, User::class);
    }
}
