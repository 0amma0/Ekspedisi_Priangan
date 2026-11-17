<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CekResiController;
use App\Http\Controllers\PaketController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\KurirController;
use App\Http\Controllers\TarifController;
use App\Http\Controllers\LaporanController;

// Halaman Publik & Auth
Route::get('/', fn() => redirect('/cek-resi'));
Route::get('/cek-resi', [CekResiController::class, 'index']);
Route::get('/login', [AuthController::class, 'form'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth');
Route::get('/cek-resi', [CekResiController::class, 'index'])->middleware('throttle:30,1');

// Grup Khusus Admin 
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/pelanggan/create', [PelangganController::class, 'create']);
    Route::post('/pelanggan', [PelangganController::class, 'store']);
    Route::get('/pelanggan/{pelanggan}/edit', [PelangganController::class, 'edit']);
    Route::put('/pelanggan/{pelanggan}', [PelangganController::class, 'update']);

    Route::get('/kurir/create', [KurirController::class, 'create']);
    Route::post('/kurir', [KurirController::class, 'store']);
    Route::post('/kurir/{kurir}/nonaktif', [KurirController::class, 'nonaktif']);
    Route::post('/kurir/{kurir}/aktif', [KurirController::class, 'aktif']);
    Route::post('/kurir/{kurir}/reset', [KurirController::class, 'reset']);

    Route::get('/paket', [PaketController::class, 'index']);
    Route::get('/paket/create', [PaketController::class, 'create']);
    Route::post('/paket', [PaketController::class, 'store']);
    Route::get('/paket/{paket}', [PaketController::class, 'show']);
    Route::post('/paket/{paket}/transit', [PaketController::class, 'transit']);
    Route::post('/paket/{paket}/tugaskan', [PaketController::class, 'tugaskan']);
    Route::post('/paket/{paket}/retur', [PaketController::class, 'retur']);
    Route::get('/cod', [PaketController::class, 'cod']);
    Route::post('/paket/{paket}/setoran', [PaketController::class, 'setoran']);
    Route::get('/kurir/{id}/edit', [KurirController::class, 'edit']);
    Route::get('/zona/cek', [PaketController::class, 'cekZona'])
        ->name('zona.cek');
});

// Grup Admin dan Manajer (hanya melihat) 
Route::middleware(['auth', 'role:admin,manajer'])->group(function () {
    Route::get('/pelanggan', [PelangganController::class, 'index']);
    Route::get('/kurir', [KurirController::class, 'index']);
    Route::get('/tarif', [TarifController::class, 'index']);

    Route::get('/akun/password', function () {
        return view('akun.password');
    })->name('akun.password');

    Route::post('/akun/password', [AuthController::class, 'gantiPassword'])
        ->name('akun.password.update');
});


// Grup Khusus Manajer (Mengubah Tarif) 
Route::middleware(['auth', 'role:manajer'])->group(function () {
    Route::post('/tarif/{zone}', [TarifController::class, 'update']);
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan');
});

Route::get('/debug-php-ssl', function () {
    return response()->json([
        'php_ini' => php_ini_loaded_file(),
        'curl_cainfo' => ini_get('curl.cainfo'),
        'openssl_cafile' => ini_get('openssl.cafile'),
    ]);
});