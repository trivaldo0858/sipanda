<?php

use App\Http\Controllers\Web\AuthController as SharedAuthController;
use App\Http\Controllers\Web\SuperAdmin\AuthController;
use App\Http\Controllers\Web\SuperAdmin\DashboardController;
use App\Http\Controllers\Web\SuperAdmin\JadwalController;
use App\Http\Controllers\Web\SuperAdmin\PenggunaController;
use App\Http\Controllers\Web\SuperAdmin\PosyanduController;
use App\Http\Controllers\Web\Posyandu\AuthController as PosyanduAuthController;
use Illuminate\Support\Facades\Route;

// Halaman utama: login user
Route::get('/', [SharedAuthController::class, 'showLogin'])
    ->name('home');

// Login bersama Bidan, Posyandu, dan Orang Tua
Route::get('/login', [SharedAuthController::class, 'showLogin'])
    ->name('login');

Route::post('/login/bidan', [SharedAuthController::class, 'loginBidan'])
    ->name('login.bidan');

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

// Login Posyandu lama: tetap dipertahankan
Route::prefix('posyandu')->name('posyandu.')->group(function () {
    Route::get('/login', [PosyanduAuthController::class, 'showLogin'])
        ->name('login');

    Route::post('/login', [PosyanduAuthController::class, 'login'])
        ->name('login.post');

    Route::post('/logout', [PosyanduAuthController::class, 'logout'])
        ->name('logout');
});