<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('guest_name', 80)->nullable();
            $table->string('guest_email', 120)->nullable();
            $table->tinyInteger('rating')->unsigned();
            $table->string('title', 120)->nullable();
            $table->text('body');
            $table->boolean('is_approved')->default(false);
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
            $table->index(['product_id', 'is_approved', 'created_at']);
            $table->index('rating');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->decimal('rating_avg', 3, 2)->default(0)->after('highlights');
            $table->unsignedInteger('rating_count')->default(0)->after('rating_avg');
            $table->index(['rating_avg', 'rating_count']);
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['rating_avg', 'rating_count']);
            $table->dropColumn(['rating_avg', 'rating_count']);
        });
        Schema::dropIfExists('product_reviews');
    }
};