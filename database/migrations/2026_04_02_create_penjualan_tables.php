<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penjualan', function (Blueprint $table) {
            $table->increments('id_penjualan');
            $table->unsignedBigInteger('id_user')->nullable();
            $table->timestamp('timestamp')->useCurrent();
            $table->integer('total')->default(0);

            $table->foreign('id_user')->references('id')->on('users')->nullOnDelete();
        });

        Schema::create('penjualan_detail', function (Blueprint $table) {
            $table->increments('idpenjualan_detail');
            $table->unsignedInteger('id_penjualan');
            $table->unsignedInteger('idbuku')->nullable();
            $table->smallInteger('jumlah');
            $table->integer('subtotal');

            $table->foreign('id_penjualan')->references('id_penjualan')->on('penjualan')->cascadeOnDelete();
            $table->foreign('idbuku')->references('idbuku')->on('buku')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penjualan_detail');
        Schema::dropIfExists('penjualan');
    }
};