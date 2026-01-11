<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    abort(403, 'Forbidden');
});

Route::middleware('auth.basic')->group(function () {
    Route::get('/admin', [App\Http\Controllers\AdminController::class, 'index']);
    Route::post('/admin/tokens', [App\Http\Controllers\AdminController::class, 'updateToken']);
});
