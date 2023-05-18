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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('subscribes_id'); // Mengubah menjadi subscribes_id
            $table->string('invoice_number');
            $table->decimal('amount', 10, 2);
            $table->string('payment_status');
            $table->date('payment_date');
            $table->string('payment_method');
            $table->timestamps();
            $table->foreign('subscribes_id')->references('id')->on('subscribe')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
