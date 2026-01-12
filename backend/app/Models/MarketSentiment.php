<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarketSentiment extends Model
{
    use HasFactory;

    protected $fillable = [
        'value',
        'label',
        'risk_regime',
        'reasoning',
    ];
}
