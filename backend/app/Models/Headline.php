<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Headline extends Model
{
    use HasFactory;

    protected $table = 'market_headlines';

    protected $fillable = [
        'title',
        'category',
        'published_at',
        'source',
        'hash',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];
}
