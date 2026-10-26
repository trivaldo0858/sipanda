<?php

use App\Http\Controllers\Web\Posyandu\AnakController as PosyanduAnakController;
use App\Http\Controllers\Web\Posyandu\PemeriksaanController as PosyanduPemeriksaanController;
use App\Http\Controllers\Web\Posyandu\JadwalController as PosyanduJadwalController;
use App\Http\Controllers\Web\AuthController as SharedAuthController;
use App\Http\Controllers\Web\SuperAdmin\AuthController;
use App\Http\Controllers\Web\SuperAdmin\DashboardController;
use App\Http\Controllers\Web\SuperAdmin\JadwalController;
use App\Http\Controllers\Web\SuperAdmin\PenggunaController;
use App\Http\Controllers\Web\SuperAdmin\PosyanduController;
use App\Http\Controllers\Web\Posyandu\AuthController as PosyanduAuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\OrangTua\DashboardController as OrangTuaDashboardController;

// Halaman utama dan login bersama
Route::get('/', [SharedAuthController::class, 'showLogin'])
    ->name('home');

Route::get('/login', [SharedAuthController::class, 'showLogin'])
    ->name('login');

// Login Bidan
Route::post('/login/bidan', [SharedAuthController::class, 'loginBidan'])
    ->name('login.bidan');

// Login dan logout Orang Tua
Route::post('/login/orang-tua', [SharedAuthController::class, 'loginOrangTua'])
    ->name('login.orang-tua');

Route::post('/orang-tua/logout', [SharedAuthController::class, 'logoutOrangTua'])
    ->name('orang-tua.logout');

// Login dan halaman Super Admin
Route::prefix('superadmin')->name('superadmin.')->group(function () {
    Route::get('/', [AuthController::class, 'showLogin'])
        ->name('login');

    Route::post('/', [AuthController::class, 'login'])
        ->name('login.post');

    Route::middleware('superadmin')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])
            ->name('logout');

        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');

        Route::get('/peta/data', [DashboardController::class, 'mapData'])
            ->name('peta.data');

        Route::resource('/posyandu', PosyanduController::class);

        Route::resource('/pengguna', PenggunaController::class);
    });
});

// Login dan halaman Posyandu
Route::prefix('posyandu')->name('posyandu.')->group(function () {
    Route::get('/login', [PosyanduAuthController::class, 'showLogin'])
        ->name('login');

    Route::post('/login', [PosyanduAuthController::class, 'login'])
        ->name('login.post');

    Route::middleware('posyandu')->group(function () {
        Route::post('/logout', [PosyanduAuthController::class, 'logout'])
            ->name('logout');

        Route::get('/anak', [PosyanduAnakController::class, 'index'])
            ->name('anak.index');

        Route::get('/anak/create', [PosyanduAnakController::class, 'create'])
            ->name('anak.create');

        Route::post('/anak', [PosyanduAnakController::class, 'store'])
            ->name('anak.store');

        Route::view('/laporan', 'posyandu.anak.laporan')
            ->name('laporan.index');

        Route::get('/pemeriksaan', [PosyanduPemeriksaanController::class, 'index'])
            ->name('pemeriksaan.index');

        Route::post('/pemeriksaan', [PosyanduPemeriksaanController::class, 'store'])
            ->name('pemeriksaan.store');

        Route::get('/jadwal', [PosyanduJadwalController::class, 'index'])
            ->name('jadwal.index');

        Route::post('/jadwal', [PosyanduJadwalController::class, 'store'])
            ->name('jadwal.store');

        Route::post('/jadwal/hubungkan-akun', [PosyanduJadwalController::class, 'linkCurrentUser'])
            ->name('jadwal.link-current-user');
    });
});


// Dashboard Orang Tua
Route::get('/orang-tua/dashboard', [
    OrangTuaDashboardController::class,
    'index',
])->name('orang-tua.dashboard');