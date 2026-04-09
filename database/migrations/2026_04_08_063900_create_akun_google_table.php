<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('akun_google', function (Blueprint $table) {
            $table->string('google_id')->primary();
            $table->unsignedBigInteger('id_user');
            $table->string('email_google');
            $table->timestamps();

            $table->foreign('id_user')->references('id_user')->on('pengguna')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('akun_google');
    }
};