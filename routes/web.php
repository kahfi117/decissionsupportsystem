<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WpController;

Route::get('/', function () {
    return redirect('/admin');
});

Route::get('/wp-test', [WpController::class, 'start'])->name('wp.rangking');
Route::get('/saw-test', [WpController::class, 'calculateSAW'])->name('saw.rangking');
Route::get('/topsis-test', [WpController::class, 'calculateTopsis'])->name('topsis.rangking');
