<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


use App\Http\Controllers\HeadlineController;

Route::get('/headlines', [HeadlineController::class, 'index']);
