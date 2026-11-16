<?php

use App\Http\Controllers\KurirApiController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [KurirApiController::class, 'login'])->middleware('throttle:10,1');

Route::middleware(['auth:sanctum', 'role:kurir'])->group(function () {
    Route::post('/logout', [KurirApiController::class, 'logout']);
    Route::post('/password', [KurirApiController::class, 'password']);

    Route::middleware('wajib.ganti')->group(function () {
        Route::get('/paket', [KurirApiController::class, 'paket']);
        Route::get('/paket/{paket}', [KurirApiController::class, 'detail']);
        Route::post('/paket/{paket}/hasil', [KurirApiController::class, 'hasil']);
        Route::get('/cod', [KurirApiController::class, 'cod']);
    });
});
