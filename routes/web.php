<?php

use App\Http\Controllers\CheckupController;
use Illuminate\Support\Facades\Route;

Route::get('/', [CheckupController::class, 'index']);
Route::post('/changeLang', [CheckupController::class, 'changeLang']);
Route::post('/checkLocation', [CheckupController::class, 'checkLocation']);
