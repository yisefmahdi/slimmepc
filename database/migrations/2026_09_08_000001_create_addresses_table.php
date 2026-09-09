<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('street');
            $table->string('house_number');
            $table->string('addition')->nullable();
            $table->string('postcode');
            $table->string('city');
            $table->string('country')->default('Nederland');
            $table->string('phone');
            $table->string('email')->nullable();
            $table->enum('type', ['billing', 'shipping'])->default('billing');
            $table->timestamps();

            $table->index('user_id');
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
