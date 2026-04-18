<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tambah kolom validasi ke pemeriksaan
        Schema::table('pemeriksaan', function (Blueprint $table) {
            $table->enum('status_validasi', ['Menunggu', 'Disetujui', 'Ditolak'])
                  ->default('Menunggu')
                  ->after('keluhan');
            $table->text('catatan_validasi')->nullable()->after('status_validasi');
            $table->string('nip_validator')->nullable()->after('catatan_validasi');
            $table->foreign('nip_validator')->references('nip')->on('bidan')->nullOnDelete();
        });

        // Tambah kolom validasi ke imunisasi
        Schema::table('imunisasi', function (Blueprint $table) {
            $table->enum('status_validasi', ['Menunggu', 'Disetujui', 'Ditolak'])
                  ->default('Menunggu')
                  ->after('tgl_pemberian');
            $table->text('catatan_validasi')->nullable()->after('status_validasi');
        });
    }

    public function down(): void
    {
        Schema::table('pemeriksaan', function (Blueprint $table) {
            $table->dropForeign(['nip_validator']);
            $table->dropColumn(['status_validasi', 'catatan_validasi', 'nip_validator']);
        });

        Schema::table('imunisasi', function (Blueprint $table) {
            $table->dropColumn(['status_validasi', 'catatan_validasi']);
        });
    }
};