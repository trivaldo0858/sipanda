<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */

    public function up(): void
    {
        Schema::create('jadwal_imunisasi', function (Blueprint $table) {
            $table->id('id_jadwal_imunisasi');

            $table->foreignId('id_vaksin')
                ->constrained('jenis_vaksin', 'id_vaksin')
                ->cascadeOnDelete();

            $table->unsignedTinyInteger('urutan_dosis')->default(1);

            // Usia sasaran pemberian vaksin
            $table->unsignedSmallInteger('usia_hari')->nullable();
            $table->unsignedSmallInteger('usia_bulan')->nullable();

            // Berlaku untuk vaksin yang hanya diberikan di wilayah tertentu
            $table->boolean('khusus_wilayah_endemis')->default(false);

            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->index(['id_vaksin', 'urutan_dosis']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal_imunisasi');
    }

};
