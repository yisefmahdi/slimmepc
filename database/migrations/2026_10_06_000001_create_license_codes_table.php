<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Digital products: license code pool per product.
     * Mirrors the old system (2025_07_01_100238) + order_item link & assigned_at.
     */
    public function up(): void
    {
        Schema::create('license_codes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->string('code')->unique();
            $table->enum('status', ['available', 'sold'])->default('available');
            $table->foreignId('order_id')->nullable()->nullOnDelete();
            $table->foreignId('order_item_id')->nullable()->nullOnDelete();
            $table->timestamp('assigned_at')->nullable();
            $table->timestamps();

            $table->index(['product_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('license_codes');
    }
};
