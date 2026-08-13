<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('content_blocks', function (Blueprint $table) {
            $table->id();
            $table->string('page', 50)->index();
            $table->string('section', 50)->index();
            $table->string('block_key', 80);
            $table->string('type', 20)->default('text');
            $table->text('value')->nullable();
            $table->json('json_value')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['page', 'section', 'block_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('content_blocks');
    }
};

