<?php
// database/migrations/2024_01_05_create_customers_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100);
            $table->string('email', 100)->nullable();
            $table->string('no_hp', 20)->nullable();
            // SC3a: foto disimpan sebagai BLOB (longblob)
            $table->binary('foto_blob')->nullable();
            // SC3b: foto disimpan sebagai file path
            $table->string('foto_path', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};