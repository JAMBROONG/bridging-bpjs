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
        Schema::create('data_klaim_bpjs_ris', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal_masuk');
            $table->date('tanggal_pulang');
            $table->integer('no_rm');
            $table->string('nama_paseien', 512);
            $table->string('no_klaim', 512);
            $table->string('inacbg', 512);
            $table->double('total_tarif');
            $table->double('tarif_rs');
            $table->timestamps();
            $table->unsignedBigInteger('users_id');
            $table->foreign('users_id')->references('id')->on('users')->onDelete('no action')->onUpdate('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_klaim_bpjs_ris');
    }
};
