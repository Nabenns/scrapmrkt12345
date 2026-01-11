<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/admin', [App\Http\Controllers\AdminController::class, 'index']);
Route::post('/admin/tokens', [App\Http\Controllers\AdminController::class, 'updateToken']);
