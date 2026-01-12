<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    abort(403, 'Forbidden');
});

Route::middleware('auth.basic')->group(function () {
    Route::get('/admin', [App\Http\Controllers\AdminController::class, 'index'])->name('admin.dashboard');
    Route::post('/admin/tokens', [App\Http\Controllers\AdminController::class, 'updateToken'])->name('admin.token.update');
    Route::post('/admin/scraper/trigger', [App\Http\Controllers\AdminController::class, 'triggerScraper'])->name('admin.scraper.trigger');
});
