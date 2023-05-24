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
        Schema::create('jenis_jasa_akuns', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('user_id');
        $table->string('kelas_tarif', 512);
        $table->enum('jenis_jasa', ['JS', 'JP'])->nullable();
        $table->timestamps();
        $table->foreign('user_id')->references('id')->on('users');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jenis_jasa_akuns');
    }
};