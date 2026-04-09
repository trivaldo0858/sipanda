<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifikasi', function (Blueprint $table) {
            $table->id('id_notifikasi');
            $table->unsignedBigInteger('id_user');
            $table->string('nik_anak')->nullable();
            $table->text('pesan');
            $table->dateTime('tgl_kirim');
            $table->enum('status', ['Belum Dibaca', 'Sudah Dibaca'])->default('Belum Dibaca');
            $table->enum('jenis_notif', ['Imunisasi', 'Posyandu', 'Umum']);
            $table->timestamps();

            $table->foreign('id_user')->references('id_user')->on('pengguna')->onDelete('cascade');
            $table->foreign('nik_anak')->references('nik_anak')->on('anak')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifikasi');
    }
};