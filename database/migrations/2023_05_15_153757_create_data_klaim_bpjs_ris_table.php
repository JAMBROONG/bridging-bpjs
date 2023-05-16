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
            $table->string('No.', 512);
            $table->string('Tgl. Masuk', 512);
            $table->string('Tgl. Pulang', 512);
            $table->integer('No. RM');
            $table->string('Nama Pasien', 512);
            $table->string('No. Klaim / SEP', 512);
            $table->string('INACBG', 512);
            $table->string('Top Up', 512);
            $table->string('Total Tarif', 512);
            $table->string('Tarif RS', 512);
            $table->string('Jenis', 512);
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
