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
        Schema::create('template_kelas_tarifs', function (Blueprint $table) {
            $table->id();
            $table->string('kelas_tarif', 512);
            $table->string('template', 512);
            $table->unsignedBigInteger('kategori_pendapatans_id');
            $table->enum('jenis_jasa', ['JS', 'JP'])->nullable();
            $table->timestamps();
            $table->foreign('kategori_pendapatans_id')->references('id')->on('kategori_pendapatans');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('template_kelas_tarifs');
    }
};
