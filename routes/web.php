<?php

use App\Http\Controllers\Web\SuperAdmin\AuthController;
use App\Http\Controllers\Web\SuperAdmin\DashboardController;
use App\Http\Controllers\Web\SuperAdmin\LaporanController;
use App\Http\Controllers\Web\SuperAdmin\PenggunaController;
use App\Http\Controllers\Web\SuperAdmin\PosyanduController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('superadmin.login');
});

Route::prefix('superadmin')->name('superadmin.')->group(function () {

    Route::get('login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AuthController::class, 'login'])->name('login.post');

    Route::middleware(['auth', 'superadmin'])->group(function () {

        Route::post('logout', [AuthController::class, 'logout'])->name('logout');
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::resource('posyandu', PosyanduController::class);

        Route::resource('pengguna', PenggunaController::class);

        Route::get('laporan', [LaporanController::class, 'index'])->name('laporan.index');
        Route::get('laporan/global', [LaporanController::class, 'globalSummary'])->name('laporan.global');
        
        
    });
});