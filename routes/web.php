<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CekResiController;
use App\Http\Controllers\PaketController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\KurirController;
use App\Http\Controllers\TarifController;
use App\Http\Controllers\LaporanController;

Route::get('/', fn() => redirect('/cek-resi'));
Route::get('/login', [AuthController::class, 'form'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth');

// Admin 
Route::middleware(['auth', 'role:admin'])->group(function () {
});

// Admin dan Manajer (hanya melihat) 
Route::middleware(['auth', 'role:admin,manajer'])->group(function () {
});

// Manajer 
Route::middleware(['auth', 'role:manajer'])->group(function () {
});
