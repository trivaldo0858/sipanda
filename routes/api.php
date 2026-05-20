<?php

use App\Http\Controllers\API\AnakController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\DashboardController;
use App\Http\Controllers\API\ImunisasiController;
use App\Http\Controllers\API\JadwalController;
use App\Http\Controllers\API\LaporanController;
use App\Http\Controllers\API\NotifikasiController;
use App\Http\Controllers\API\PemeriksaanController;
use App\Http\Controllers\API\PosyanduController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    // ================================================================
    // PUBLIC — tidak perlu login
    // ================================================================

    // Dropdown posyandu untuk login Kader
    Route::get('posyandu/list', [PosyanduController::class, 'list']);
    Route::get('posyandu/{id}', [PosyanduController::class, 'show']);

    // Login per role
    Route::prefix('auth')->group(function () {
        Route::post('login/kader',      [AuthController::class, 'loginKader']);
        Route::post('login/bidan',      [AuthController::class, 'loginBidan']);
        Route::post('login/orang-tua',  [AuthController::class, 'loginOrangTua']);
    });

    // ================================================================
    // PROTECTED — wajib login (Sanctum token)
    // ================================================================
    Route::middleware('auth:sanctum')->group(function () {

        // ── Auth ──────────────────────────────────────────────────────
        Route::prefix('auth')->group(function () {
            Route::post('logout',              [AuthController::class, 'logout']);
            Route::get('me',                   [AuthController::class, 'me']);
            Route::post('ubah-password',       [AuthController::class, 'ubahPassword']);
            Route::post('ubah-password-kader', [AuthController::class, 'ubahPasswordKader']);
        });

        // ── Dashboard ─────────────────────────────────────────────────
        Route::get('dashboard', [DashboardController::class, 'index']);

        // ── Notifikasi — semua role ───────────────────────────────────
        Route::prefix('notifikasi')->group(function () {
            Route::get('/',              [NotifikasiController::class, 'index']);
            Route::get('unread-count',   [NotifikasiController::class, 'unreadCount']);
            Route::post('mark-all-read', [NotifikasiController::class, 'markAllRead']);
            Route::post('{id}/read',     [NotifikasiController::class, 'markRead']);
            Route::delete('{id}',        [NotifikasiController::class, 'destroy']);
        });

        // ── Profil Posyandu — Kader ───────────────────────────────────
        Route::middleware('role:Kader')->group(function () {
            Route::get('posyandu/profil',    [PosyanduController::class, 'profil']);
            Route::put('posyandu/profil',    [PosyanduController::class, 'updateProfil']);
        });

        // ── Data Anak — Kader kelola, Bidan & OrangTua lihat ─────────
        Route::get('anak',                    [AnakController::class, 'index']);
        Route::get('anak/{nik}',              [AnakController::class, 'show']);
        Route::get('anak/{nik}/perkembangan', [AnakController::class, 'perkembangan']);

        Route::middleware('role:Kader')->group(function () {
            Route::post('anak',         [AnakController::class, 'store']);
            Route::put('anak/{nik}',    [AnakController::class, 'update']);
            Route::delete('anak/{nik}', [AnakController::class, 'destroy']);
        });

        // ── Pemeriksaan — Kader input, Bidan validasi ─────────────────
        Route::get('pemeriksaan',      [PemeriksaanController::class, 'index']);
        Route::get('pemeriksaan/{id}', [PemeriksaanController::class, 'show']);

        Route::middleware('role:Kader')->group(function () {
            Route::post('pemeriksaan',        [PemeriksaanController::class, 'store']);
            Route::put('pemeriksaan/{id}',    [PemeriksaanController::class, 'update']);
            Route::delete('pemeriksaan/{id}', [PemeriksaanController::class, 'destroy']);
        });

        Route::middleware('role:Bidan')->group(function () {
            Route::patch('pemeriksaan/{id}/validasi', [PemeriksaanController::class, 'validasi']);
        });

        // ── Imunisasi — Bidan input & kelola ─────────────────────────
        Route::get('imunisasi',             [ImunisasiController::class, 'index']);
        Route::get('imunisasi/jenis-vaksin',[ImunisasiController::class, 'jenisVaksin']);
        Route::get('imunisasi/{id}',        [ImunisasiController::class, 'show']);
        Route::get('imunisasi/riwayat/{nik}',[ImunisasiController::class, 'riwayat']);

        Route::middleware('role:Bidan')->group(function () {
            Route::post('imunisasi',        [ImunisasiController::class, 'store']);
            Route::put('imunisasi/{id}',    [ImunisasiController::class, 'update']);
            Route::delete('imunisasi/{id}', [ImunisasiController::class, 'destroy']);
        });

        // ── Jadwal Posyandu — Kader kelola, semua lihat ───────────────
        Route::get('jadwal',      [JadwalController::class, 'index']);
        Route::get('jadwal/{id}', [JadwalController::class, 'show']);

        Route::middleware('role:Kader')->group(function () {
            Route::post('jadwal',        [JadwalController::class, 'store']);
            Route::put('jadwal/{id}',    [JadwalController::class, 'update']);
            Route::delete('jadwal/{id}', [JadwalController::class, 'destroy']);
        });

        // ── Laporan — Kader & Bidan ───────────────────────────────────
        Route::middleware('role:Kader,Bidan')->group(function () {
            Route::get('laporan',                   [LaporanController::class, 'index']);
            Route::post('laporan',                  [LaporanController::class, 'store']);
            Route::get('laporan/{id}',              [LaporanController::class, 'show']);
            Route::delete('laporan/{id}',           [LaporanController::class, 'destroy']);
            Route::get('laporan/{id}/export-pdf',   [LaporanController::class, 'exportPdf']);
            Route::get('laporan/{id}/export-excel', [LaporanController::class, 'exportExcel']);
        });

    }); // end auth:sanctum

});