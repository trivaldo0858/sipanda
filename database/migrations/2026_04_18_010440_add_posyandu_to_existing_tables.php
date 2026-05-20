<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Tambah id_posyandu_aktif ke pengguna ──────────────────────
        // id_posyandu sudah ada di migration sebelumnya, jadi skip
        // Hanya tambah yang belum ada
        Schema::table('pengguna', function (Blueprint $table) {
            // Cek dulu sebelum tambah — hindari error duplicate
            if (! Schema::hasColumn('pengguna', 'id_posyandu_aktif')) {
                $table->unsignedBigInteger('id_posyandu_aktif')
                      ->nullable()
                      ->after('id_posyandu');

                $table->foreign('id_posyandu_aktif')
                      ->references('id_posyandu')
                      ->on('posyandu')
                      ->nullOnDelete();
            }
        });

        // ── Tabel pivot pengguna_posyandu ─────────────────────────────
        if (! Schema::hasTable('pengguna_posyandu')) {
            Schema::create('pengguna_posyandu', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('id_user');
                $table->unsignedBigInteger('id_posyandu');
                $table->timestamps();

                $table->foreign('id_user')
                      ->references('id_user')
                      ->on('pengguna')
                      ->onDelete('cascade');

                $table->foreign('id_posyandu')
                      ->references('id_posyandu')
                      ->on('posyandu')
                      ->onDelete('cascade');

                $table->unique(['id_user', 'id_posyandu']);
            });
        }

        // ── Tambah kolom validasi ke pemeriksaan ──────────────────────
        if (Schema::hasTable('pemeriksaan')) {
            Schema::table('pemeriksaan', function (Blueprint $table) {
                if (! Schema::hasColumn('pemeriksaan', 'status_validasi')) {
                    $table->enum('status_validasi', ['Menunggu', 'Disetujui', 'Ditolak'])
                          ->default('Menunggu')
                          ->after('keluhan');
                }
                if (! Schema::hasColumn('pemeriksaan', 'catatan_validasi')) {
                    $table->text('catatan_validasi')->nullable()->after('status_validasi');
                }
            });
        }
    }

    public function down(): void
    {
        Schema::table('pengguna', function (Blueprint $table) {
            if (Schema::hasColumn('pengguna', 'id_posyandu_aktif')) {
                $table->dropForeign(['id_posyandu_aktif']);
                $table->dropColumn('id_posyandu_aktif');
            }
        });

        Schema::dropIfExists('pengguna_posyandu');

        if (Schema::hasTable('pemeriksaan')) {
            Schema::table('pemeriksaan', function (Blueprint $table) {
                if (Schema::hasColumn('pemeriksaan', 'status_validasi')) {
                    $table->dropColumn(['status_validasi', 'catatan_validasi']);
                }
            });
        }
    }
};