<?php
// database/migrations/2024_01_04_create_toko_tables.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guests', function (Blueprint $table) {
            $table->id();
            $table->string('kode_guest', 20)->unique(); // Guest_0000001
            $table->string('nama', 100)->nullable();
            $table->string('phone', 20)->nullable();
            $table->timestamps();
        });

        Schema::create('pesanan', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('guest_id');
            $table->string('kode_pesanan', 30)->unique();
            $table->integer('total');
            $table->enum('status_bayar', ['pending','lunas','expired','failed'])->default('pending');
            // Midtrans
            $table->string('snap_token', 500)->nullable();
            $table->string('payment_type', 50)->nullable();
            $table->string('va_number', 100)->nullable();
            $table->string('midtrans_order_id', 100)->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->foreign('guest_id')->references('id')->on('guests')->cascadeOnDelete();
        });

        Schema::create('pesanan_detail', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pesanan_id');
            $table->unsignedInteger('idbuku');
            $table->integer('harga');
            $table->smallInteger('jumlah');
            $table->integer('subtotal');

            $table->foreign('pesanan_id')->references('id')->on('pesanan')->cascadeOnDelete();
            $table->foreign('idbuku')->references('idbuku')->on('buku')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pesanan_detail');
        Schema::dropIfExists('pesanan');
        Schema::dropIfExists('guests');
    }
};