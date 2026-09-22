<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// IDENTIEK aan oud project (2025_04_07_090033) — nodig voor data-synchronisatie.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('technician_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->integer('value');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('technician_settings');
    }
};
