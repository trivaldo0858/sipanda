<?php

use App\Http\Controllers\Web\SuperAdmin\AuthController;
use App\Http\Controllers\Web\SuperAdmin\DashboardController;
use App\Http\Controllers\Web\SuperAdmin\JadwalController;
use App\Http\Controllers\Web\SuperAdmin\LaporanController;
use App\Http\Controllers\Web\SuperAdmin\PenggunaController;
use App\Http\Controllers\Web\SuperAdmin\PosyanduController;
use Illuminate\Support\Facades\Route;

// --- TAMBAHKAN INI ---
Route::get('/', function () {
    return redirect()->route('superadmin.login');
});

// ── Super Admin Web Routes ────────────────────────────────────────────
Route::prefix('superadmin')->name('superadmin.')->group(function () {

    // Login (public)
    Route::get('login',  [AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AuthController::class, 'login'])->name('login.post');

    // Protected — wajib login sebagai SuperAdmin
    Route::middleware('superadmin')->group(function () {

        Route::post('logout', [AuthController::class, 'logout'])->name('logout');

        // Dashboard global
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        // Manajemen Posyandu
        Route::resource('posyandu', PosyanduController::class);

        // Manajemen Pengguna
        Route::resource('pengguna', PenggunaController::class);

        // Laporan lintas posyandu
        Route::get('laporan',         [LaporanController::class, 'index'])->name('laporan.index');
        Route::get('laporan/global',  [LaporanController::class, 'globalSummary'])->name('laporan.global');
    });
});