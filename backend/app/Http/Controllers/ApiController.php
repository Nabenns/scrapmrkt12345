<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MarketSentiment;
use App\Models\EconomicSummary;
use App\Models\TrumpEvent;
use App\Models\TrumpVolatility;
use App\Models\EtfSummary;

class ApiController extends Controller
{
    public function sentiment()
    {
        return response()->json(MarketSentiment::latest()->first());
    }

    public function economic()
    {
        return response()->json(EconomicSummary::latest()->first());
    }

    public function trumpEvents()
    {
        return response()->json(TrumpEvent::orderBy('date', 'desc')->orderBy('time', 'desc')->take(50)->get());
    }

    public function trumpVolatility()
    {
        return response()->json(TrumpVolatility::latest()->first());
    }

    public function etf()
    {
        return response()->json(EtfSummary::latest()->first());
    }
}
