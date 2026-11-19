<?php

use App\Http\Controllers\KurirApiController;
use Illuminate\Support\Facades\Route;

// LOGIN
Route::post('/login', [KurirApiController::class, 'login'])
    ->middleware('throttle:10,1');

// RESET PASSWORD - TIDAK PERLU LOGIN
Route::post('/password/forgot', [KurirApiController::class, 'forgotPassword'])
    ->middleware('throttle:10,1');

Route::post('/password/reset', [KurirApiController::class, 'resetPassword'])
    ->middleware('throttle:5,1');

// FITUR KURIR - WAJIB LOGIN
Route::middleware(['auth:sanctum', 'role:kurir'])->group(function () {

    Route::post('/logout', [KurirApiController::class, 'logout']);
    Route::post('/password', [KurirApiController::class, 'password']);

    Route::get('/akun', [KurirApiController::class, 'akun']);
    Route::put('/akun/email', [KurirApiController::class, 'email']);

    Route::middleware('wajib.ganti')->group(function () {
        Route::get('/paket', [KurirApiController::class, 'paket']);
        Route::get('/paket/{paket}', [KurirApiController::class, 'detail']);
        Route::post('/paket/{paket}/hasil', [KurirApiController::class, 'hasil']);
        Route::get('/cod', [KurirApiController::class, 'cod']);
    });
});
