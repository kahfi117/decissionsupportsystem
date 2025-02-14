<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WpController;
use App\Http\Controllers\DssController;

Route::get('/', function () {
    return redirect('/admin');
});

Route::get('/wp-test', [WpController::class, 'calculateWp'])->name('wp.rangking');
Route::get('/saw-test', [WpController::class, 'calculateSAW'])->name('saw.rangking');
Route::get('/topsis-test', [WpController::class, 'calculateTopsis'])->name('topsis.rangking');
Route::get('/dss/saw-calculation/{topicId}', [DssController::class, 'sawCalculation'])->name('dss.saw.calculation');
Route::get('/dss/topsis-calculation/{topicId}', [DssController::class, 'topsisCalculation'])->name('dss.saw.calculation');