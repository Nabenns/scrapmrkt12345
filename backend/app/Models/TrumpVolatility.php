<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrumpVolatility extends Model
{
    use HasFactory;

    protected $table = 'trump_volatility'; // Non-standard pluralization

    protected $fillable = [
        'score',
        'explanation',
    ];
}
