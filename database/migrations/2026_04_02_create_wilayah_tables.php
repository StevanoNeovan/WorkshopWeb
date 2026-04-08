<?php
// database/migrations/2024_01_03_create_wilayah_tables.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('provinces', function (Blueprint $table) {
            $table->string('id', 2)->primary();
            $table->string('name', 100);
        });

        Schema::create('regencies', function (Blueprint $table) {
            $table->string('id', 4)->primary();
            $table->string('province_id', 2);
            $table->string('name', 100);
            $table->foreign('province_id')->references('id')->on('provinces')->cascadeOnDelete();
        });

        Schema::create('districts', function (Blueprint $table) {
            $table->string('id', 7)->primary();
            $table->string('regency_id', 4);
            $table->string('name', 100);
            $table->foreign('regency_id')->references('id')->on('regencies')->cascadeOnDelete();
        });

        Schema::create('villages', function (Blueprint $table) {
            $table->string('id', 10)->primary();
            $table->string('district_id', 7);
            $table->string('name', 100);
            $table->foreign('district_id')->references('id')->on('districts')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('villages');
        Schema::dropIfExists('districts');
        Schema::dropIfExists('regencies');
        Schema::dropIfExists('provinces');
    }
};