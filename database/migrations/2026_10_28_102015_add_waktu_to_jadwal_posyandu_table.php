<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('jadwal_posyandu', function (Blueprint $table) {
            $table->time('waktu')->nullable()->after('tgl_kegiatan');
        });
    }

    public function down(): void
    {
        Schema::table('jadwal_posyandu', function (Blueprint $table) {
            $table->dropColumn('waktu');
        });
    }
};
