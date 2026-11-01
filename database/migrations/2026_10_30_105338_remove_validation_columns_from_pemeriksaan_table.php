<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        $columns = [];

        if (Schema::hasColumn('pemeriksaan', 'status_validasi')) {
            $columns[] = 'status_validasi';
        }

        if (Schema::hasColumn('pemeriksaan', 'catatan_validasi')) {
            $columns[] = 'catatan_validasi';
        }

        if (!empty($columns)) {
            Schema::table('pemeriksaan', function (Blueprint $table) use ($columns) {
                $table->dropColumn($columns);
            });
        }
    }

    public function down(): void
    {
        Schema::table('pemeriksaan', function (Blueprint $table) {
            if (!Schema::hasColumn('pemeriksaan', 'status_validasi')) {
                $table->enum('status_validasi', [
                    'Menunggu',
                    'Disetujui',
                    'Ditolak',
                ])->default('Menunggu')->after('keluhan');
            }

            if (!Schema::hasColumn('pemeriksaan', 'catatan_validasi')) {
                $table->text('catatan_validasi')
                    ->nullable()
                    ->after('status_validasi');
            }
        });
    }
};

