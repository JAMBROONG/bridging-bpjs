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
        Schema::create('kpi_dokters', function (Blueprint $table) {
            $table->id();
            $table->string('kelompok');
            $table->string('nilai');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->unsignedBigInteger('kategori_id');
            $table->foreign('kategori_id')->references('id')->on('kpi_kategoris');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */ 
    public function down(): void
    {
        Schema::dropIfExists('kpi_dokters');
    }
};
