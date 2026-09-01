<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
            $table->string('slug')->unique();
            $table->string('title');
            $table->string('brand')->nullable();
            $table->string('sku', 64)->nullable()->unique();
            $table->decimal('price', 10, 2);
            $table->decimal('old_price', 10, 2)->nullable();
            $table->enum('discount_type', ['percentage', 'fixed'])->nullable();
            $table->decimal('discount_value', 10, 2)->nullable();
            $table->dateTime('discount_start_date')->nullable();
            $table->dateTime('discount_end_date')->nullable();
            $table->enum('stock_status', ['in_stock', 'out_of_stock'])->default('in_stock');
            $table->boolean('status')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->text('description')->nullable();
            $table->json('features')->nullable();
            $table->json('colors')->nullable();
            $table->json('sizes')->nullable();
            $table->string('main_image')->nullable();
            $table->json('gallery_images')->nullable();
            $table->string('external_link')->nullable();
            $table->string('delivery_time')->nullable();
            $table->string('download_32bit_url')->nullable();
            $table->string('download_64bit_url')->nullable();
            $table->string('manual_url')->nullable();
            $table->timestamps();

            $table->index('category_id');
            $table->index(['status', 'stock_status']);
            $table->index('brand');
            $table->index('price');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
