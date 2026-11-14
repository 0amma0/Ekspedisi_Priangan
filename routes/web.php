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
});

// Grup Admin dan Manajer (hanya melihat) 
Route::middleware(['auth', 'role:admin,manajer'])->group(function () {
    Route::get('/pelanggan', [PelangganController::class, 'index']);
    Route::get('/kurir', [KurirController::class, 'index']);
    Route::get('/tarif', [TarifController::class, 'index']);
});

// Grup Khusus Manajer (Mengubah Tarif) 
Route::middleware(['auth', 'role:manajer'])->group(function () {
    Route::post('/tarif/{zone}', [TarifController::class, 'update']);
});
