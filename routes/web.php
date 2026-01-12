<?php

use App\Http\Controllers\TotpController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('index');
});

Route::get('/add/manual', function () {
    return view('add-manual');
});

Route::get('/add/uri', function () {
    return view('add-uri');
});

Route::prefix('api')->group(function () {
    Route::get('/totp', [TotpController::class, 'index']);
    Route::post('/totp', [TotpController::class, 'store']);
    Route::post('/totp/uri', [TotpController::class, 'storeFromUri']);
    Route::get('/totp/{entry}', [TotpController::class, 'getCode']);
    Route::delete('/totp/{entry}', [TotpController::class, 'destroy']);
});
