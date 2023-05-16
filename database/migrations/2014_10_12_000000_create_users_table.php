<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('direktur');
            $table->date('tanggal_pendaftaran');
            $table->string('alamat');
            $table->string('telepon');
            $table->string('logo');
            $table->string('website');
            $table->string('kepemilikan');
            $table->string('luas_tanah');
            $table->string('luas_bangunan');
            $table->enum('kelas', ['A', 'B','C','D','Belum diterapkan'])->nullable();
            $table->enum('status_blu', ['BLUD', 'Non BLU'])->nullable();
            $table->string('email')->unique();
            $table->string('npwp')->unique();
            $table->string('akte_pendirian');
            $table->string('surat_izin_usaha');
            $table->string('nomor_registrasi_bpjs')->unique();
            $table->string('klasifikasi_lapangan_usaha')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
