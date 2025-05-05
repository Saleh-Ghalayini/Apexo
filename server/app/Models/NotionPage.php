<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotionPage extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'page_id',
        'title',
        'created_at',
        'updated_at',
    ];
}
