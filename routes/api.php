<?php

use App\Http\Controllers\API\AnakController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\DashboardController;
use App\Http\Controllers\API\ImunisasiController;
use App\Http\Controllers\API\JadwalPosyanduController;
use App\Http\Controllers\API\JenisVaksinController;
use App\Http\Controllers\API\LaporanController;
use App\Http\Controllers\API\NotifikasiController;
use App\Http\Controllers\API\PemeriksaanController;
use App\Http\Controllers\API\PenggunaController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| SIPANDA — API Routes
| Base URL: /api/v1
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {

    // ============================================================
    // PUBLIC — tidak perlu login
    // ============================================================
    Route::prefix('auth')->group(function () {
        Route::post('login', [AuthController::class, 'login']);
        Route::post('login-google', [AuthController::class, 'loginGoogle']);
    });

    // ============================================================
    // PROTECTED — wajib login (Sanctum token)
    // ============================================================
    Route::middleware('auth:sanctum')->group(function () {

        // Auth
        Route::prefix('auth')->group(function () {
            Route::post('logout', [AuthController::class, 'logout']);
            Route::get('me', [AuthController::class, 'me']);
            Route::post('ubah-password', [AuthController::class, 'ubahPassword']);
        });

        // Dashboard (semua role, response disesuaikan di controller)
        Route::get('dashboard', [DashboardController::class, 'index']);

        // --------------------------------------------------------
        // Notifikasi — semua role (hanya milik sendiri)
        // --------------------------------------------------------
        Route::prefix('notifikasi')->group(function () {
            Route::get('/', [NotifikasiController::class, 'index']);
            Route::post('mark-all-read', [NotifikasiController::class, 'markAllRead']);
            Route::post('{id}/read', [NotifikasiController::class, 'markRead']);
            Route::delete('{id}', [NotifikasiController::class, 'destroy']);
        });

        // --------------------------------------------------------
        // Jadwal Posyandu — semua role bisa lihat
        // --------------------------------------------------------
        Route::get('jadwal', [JadwalPosyanduController::class, 'index']);
        Route::get('jadwal/{id}', [JadwalPosyanduController::class, 'show']);

        // Jadwal — Kader & Bidan bisa create/update/delete
        Route::middleware('role:Kader,Bidan')->group(function () {
            Route::post('jadwal', [JadwalPosyanduController::class, 'store']);
            Route::put('jadwal/{id}', [JadwalPosyanduController::class, 'update']);
            Route::delete('jadwal/{id}', [JadwalPosyanduController::class, 'destroy']);
        });

        // --------------------------------------------------------
        // Jenis Vaksin — semua role bisa lihat
        // --------------------------------------------------------
        Route::get('vaksin', [JenisVaksinController::class, 'index']);
        Route::get('vaksin/{id}', [JenisVaksinController::class, 'show']);

        // Vaksin — hanya Bidan yang bisa kelola
        Route::middleware('role:Bidan')->group(function () {
            Route::post('vaksin', [JenisVaksinController::class, 'store']);
            Route::put('vaksin/{id}', [JenisVaksinController::class, 'update']);
            Route::delete('vaksin/{id}', [JenisVaksinController::class, 'destroy']);
        });

        // --------------------------------------------------------
        // Data Anak — Kader & Bidan akses penuh, OrangTua read own
        // --------------------------------------------------------
        Route::get('anak', [AnakController::class, 'index']);
        Route::get('anak/{nik}', [AnakController::class, 'show']);
        Route::get('anak/{nik}/perkembangan', [AnakController::class, 'perkembangan']);

        Route::middleware('role:Kader,Bidan')->group(function () {
            Route::post('anak', [AnakController::class, 'store']);
            Route::put('anak/{nik}', [AnakController::class, 'update']);
            Route::delete('anak/{nik}', [AnakController::class, 'destroy']);
        });

        // --------------------------------------------------------
        // Pemeriksaan — Kader & Bidan kelola, OrangTua bisa read
        // --------------------------------------------------------
        Route::get('pemeriksaan', [PemeriksaanController::class, 'index']);
        Route::get('pemeriksaan/{id}', [PemeriksaanController::class, 'show']);

        Route::middleware('role:Kader,Bidan')->group(function () {
            Route::post('pemeriksaan', [PemeriksaanController::class, 'store']);
            Route::put('pemeriksaan/{id}', [PemeriksaanController::class, 'update']);
            Route::delete('pemeriksaan/{id}', [PemeriksaanController::class, 'destroy']);
        });

        // --------------------------------------------------------
        // Imunisasi — Bidan & Kader kelola, OrangTua bisa read
        // --------------------------------------------------------
        Route::get('imunisasi', [ImunisasiController::class, 'index']);
        Route::get('imunisasi/{id}', [ImunisasiController::class, 'show']);

        Route::middleware('role:Bidan,Kader')->group(function () {
            Route::post('imunisasi', [ImunisasiController::class, 'store']);
            Route::put('imunisasi/{id}', [ImunisasiController::class, 'update']);
            Route::delete('imunisasi/{id}', [ImunisasiController::class, 'destroy']);
        });

        // --------------------------------------------------------
        // Laporan — Bidan only
        // --------------------------------------------------------
        Route::middleware('role:Bidan')->group(function () {
            Route::get('laporan', [LaporanController::class, 'index']);
            Route::post('laporan', [LaporanController::class, 'store']);
            Route::get('laporan/{id}', [LaporanController::class, 'show']);
            Route::delete('laporan/{id}', [LaporanController::class, 'destroy']);
        });

        // --------------------------------------------------------
        // Manajemen Pengguna — Kader only
        // --------------------------------------------------------
        Route::middleware('role:Kader')->group(function () {
            Route::apiResource('pengguna', PenggunaController::class);
        });

    }); // end auth:sanctum

});