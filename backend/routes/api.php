<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


use App\Http\Controllers\HeadlineController;

Route::middleware('auth.apikey')->group(function () {
    Route::get('/headlines', [HeadlineController::class, 'index']);
    
    // New Data Endpoints
    Route::get('/sentiment', [App\Http\Controllers\ApiController::class, 'sentiment']);
    Route::get('/economic', [App\Http\Controllers\ApiController::class, 'economic']);
    Route::get('/trump/events', [App\Http\Controllers\ApiController::class, 'trumpEvents']);
    Route::get('/trump/volatility', [App\Http\Controllers\ApiController::class, 'trumpVolatility']);
    Route::get('/etf', [App\Http\Controllers\ApiController::class, 'etf']);
});
