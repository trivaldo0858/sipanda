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
use App\Http\Controllers\API\PosyanduController;
use App\Http\Controllers\API\ValidasiController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    // ── PUBLIC ───────────────────────────────────────────────────────
    Route::prefix('auth')->group(function () {
        Route::post('login',        [AuthController::class, 'login']);
        Route::post('login-ortu',   [AuthController::class, 'loginOrangTua']);
        Route::post('login-google', [AuthController::class, 'loginGoogle']);
    });

    // ── PROTECTED ────────────────────────────────────────────────────
    Route::middleware('auth:sanctum')->group(function () {

        // Auth
        Route::prefix('auth')->group(function () {
            Route::post('logout',        [AuthController::class, 'logout']);
            Route::get('me',             [AuthController::class, 'me']);
            Route::post('ubah-password', [AuthController::class, 'ubahPassword']);
        });

        // Dashboard
        Route::get('dashboard', [DashboardController::class, 'index']);

        // Notifikasi
        Route::prefix('notifikasi')->group(function () {
            Route::get('/',              [NotifikasiController::class, 'index']);
            Route::post('mark-all-read', [NotifikasiController::class, 'markAllRead']);
            Route::post('{id}/read',     [NotifikasiController::class, 'markRead']);
            Route::delete('{id}',        [NotifikasiController::class, 'destroy']);
        });

        // Posyandu — semua bisa lihat
        Route::get('posyandu',      [PosyanduController::class, 'index']);
        Route::get('posyandu/{id}', [PosyanduController::class, 'show']);
        // Posyandu CRUD — Super Admin only (via Sanctum token jika Super Admin login mobile)
        Route::middleware('role:SuperAdmin')->group(function () {
            Route::post('posyandu',         [PosyanduController::class, 'store']);
            Route::put('posyandu/{id}',     [PosyanduController::class, 'update']);
            Route::delete('posyandu/{id}',  [PosyanduController::class, 'destroy']);
        });

        // Jadwal — semua bisa lihat
        Route::get('jadwal',      [JadwalPosyanduController::class, 'index']);
        Route::get('jadwal/{id}', [JadwalPosyanduController::class, 'show']);
        Route::middleware('role:Kader,Bidan,SuperAdmin')->group(function () {
            Route::post('jadwal',        [JadwalPosyanduController::class, 'store']);
            Route::put('jadwal/{id}',    [JadwalPosyanduController::class, 'update']);
            Route::delete('jadwal/{id}', [JadwalPosyanduController::class, 'destroy']);
        });

        // Vaksin
        Route::get('vaksin',      [JenisVaksinController::class, 'index']);
        Route::get('vaksin/{id}', [JenisVaksinController::class, 'show']);
        Route::middleware('role:Bidan,SuperAdmin')->group(function () {
            Route::post('vaksin',        [JenisVaksinController::class, 'store']);
            Route::put('vaksin/{id}',    [JenisVaksinController::class, 'update']);
            Route::delete('vaksin/{id}', [JenisVaksinController::class, 'destroy']);
        });

        // Anak
        Route::get('anak',                    [AnakController::class, 'index']);
        Route::get('anak/{nik}',              [AnakController::class, 'show']);
        Route::get('anak/{nik}/perkembangan', [AnakController::class, 'perkembangan']);
        Route::middleware('role:Kader,Bidan,SuperAdmin')->group(function () {
            Route::post('anak',          [AnakController::class, 'store']);
            Route::put('anak/{nik}',     [AnakController::class, 'update']);
            Route::delete('anak/{nik}',  [AnakController::class, 'destroy']);
        });

        // Pemeriksaan
        Route::get('pemeriksaan',      [PemeriksaanController::class, 'index']);
        Route::get('pemeriksaan/{id}', [PemeriksaanController::class, 'show']);
        Route::middleware('role:Kader,Bidan,SuperAdmin')->group(function () {
            Route::post('pemeriksaan',         [PemeriksaanController::class, 'store']);
            Route::put('pemeriksaan/{id}',     [PemeriksaanController::class, 'update']);
            Route::delete('pemeriksaan/{id}',  [PemeriksaanController::class, 'destroy']);
        });

        // Imunisasi
        Route::get('imunisasi',      [ImunisasiController::class, 'index']);
        Route::get('imunisasi/{id}', [ImunisasiController::class, 'show']);
        Route::middleware('role:Bidan,Kader,SuperAdmin')->group(function () {
            Route::post('imunisasi',        [ImunisasiController::class, 'store']);
            Route::put('imunisasi/{id}',    [ImunisasiController::class, 'update']);
            Route::delete('imunisasi/{id}', [ImunisasiController::class, 'destroy']);
        });

        // Validasi — Bidan only
        Route::middleware('role:Bidan')->group(function () {
            Route::get('validasi',                               [ValidasiController::class, 'index']);
            Route::patch('validasi/pemeriksaan/{id}',            [ValidasiController::class, 'validasiPemeriksaan']);
            Route::patch('validasi/imunisasi/{id}',              [ValidasiController::class, 'validasiImunisasi']);
        });

        // Laporan — Bidan & Kader
        Route::middleware('role:Bidan,Kader,SuperAdmin')->group(function () {
            Route::get('laporan',                    [LaporanController::class, 'index']);
            Route::post('laporan',                   [LaporanController::class, 'store']);
            Route::get('laporan/{id}',               [LaporanController::class, 'show']);
            Route::delete('laporan/{id}',            [LaporanController::class, 'destroy']);
            Route::get('laporan/{id}/export-pdf',    [LaporanController::class, 'exportPdf']);
            Route::get('laporan/{id}/export-excel',  [LaporanController::class, 'exportExcel']);
        });

        // Pengguna — Kader & SuperAdmin
        Route::middleware('role:Kader,SuperAdmin')->group(function () {
            Route::apiResource('pengguna', PenggunaController::class);
        });

    });
});