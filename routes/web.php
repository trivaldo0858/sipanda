<?php

use App\Http\Controllers\Web\SuperAdmin\AuthController;
use App\Http\Controllers\Web\SuperAdmin\DashboardController;
use App\Http\Controllers\Web\SuperAdmin\LaporanController;
use App\Http\Controllers\Web\SuperAdmin\PenggunaController;
use App\Http\Controllers\Web\SuperAdmin\PosyanduController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// 1. REDIRECT OTOMATIS KE DASHBOARD (Bypass Login)
Route::get('/', function () {
    // Jika belum login, paksa login pakai ID 1 (User pertama di database)
    if (!Auth::check()) {
        Auth::loginUsingId(1);
    }
    return redirect()->route('superadmin.dashboard');
});

Route::prefix('superadmin')->name('superadmin.')->group(function () {

    // Route Login (Tetap ada buat jaga-jaga)
    Route::get('login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AuthController::class, 'login'])->name('login.post');

    // Group Middleware (Sekarang lebih aman dari duplikasi)
    Route::middleware(['auth', 'superadmin'])->group(function () {

        Route::post('logout', [AuthController::class, 'logout'])->name('logout');

        // DASHBOARD UTAMA
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // CRUD POSYANDU & PENGGUNA
        Route::resource('posyandu', PosyanduController::class);
        Route::resource('pengguna', PenggunaController::class);

        // LAPORAN
        Route::get('laporan', [LaporanController::class, 'index'])->name('laporan.index');
        Route::get('laporan/global', [LaporanController::class, 'globalSummary'])->name('laporan.global');

        // ROUTE PLACEHOLDER (Agar Sidebar tidak error)
        Route::get('/registrasi', [PosyanduController::class, 'create'])->name('posyandu.create.alias');
        Route::get('/staff', function () {
            return "Halaman Staff"; })->name('staff.index');
        Route::get('/akun-staff', [PenggunaController::class, 'index'])->name('akun.index');
    });
});