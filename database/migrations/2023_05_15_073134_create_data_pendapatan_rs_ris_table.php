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
        Schema::create('data_pendapatan_rs_ris', function (Blueprint $table) {
            $table->id();
            $table->integer('rm')->nullable();
            $table->string('notrans', 512)->nullable();
            $table->string('tanggal', 512)->nullable();
            $table->string('pasien', 512)->nullable();
            $table->string('unit', 512)->nullable();
            $table->string('faktur', 512)->nullable();
            $table->string('produk', 512)->nullable();
            $table->string('obat', 512)->nullable();
            $table->string('qty', 512)->nullable();
            $table->string('tarip', 512)->nullable();
            $table->string('jumlah', 512)->nullable();
            $table->string('dokter', 512)->nullable();
            $table->string('penjamin', 512)->nullable();
            $table->string('invoice', 512)->nullable();
            $table->string('bayar', 512)->nullable();
            $table->timestamps();
            $table->enum('kategori_layanan', ['JP', 'JS'])->nullable();
            $table->enum('klasifikasi', ['obat', 'kamar', 'kamar operasi', 'administrasi', 'faskes'])->nullable();
            $table->unsignedBigInteger('users_id');
            $table->foreign('users_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_pendapatan_rs_ri');
    }
};
