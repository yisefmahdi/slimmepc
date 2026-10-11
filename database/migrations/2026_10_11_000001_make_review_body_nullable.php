<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            // SQLite has no MODIFY — rebuild the table with a nullable body.
            Schema::create('product_reviews_new', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('guest_name', 80)->nullable();
                $table->string('guest_email', 120)->nullable();
                $table->tinyInteger('rating')->unsigned();
                $table->string('title', 120)->nullable();
                $table->text('body')->nullable();
                $table->boolean('is_approved')->default(false);
                $table->string('ip_address', 45)->nullable();
                $table->timestamps();
                $table->index(['product_id', 'is_approved', 'created_at']);
                $table->index('rating');
            });
            DB::statement('INSERT INTO `product_reviews_new` SELECT * FROM `product_reviews`');
            Schema::drop('product_reviews');
            Schema::rename('product_reviews_new', 'product_reviews');
            return;
        }

        DB::statement('ALTER TABLE `product_reviews` MODIFY `body` TEXT NULL');
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            DB::table('product_reviews')->whereNull('body')->update(['body' => '']);
            Schema::create('product_reviews_old', function (Blueprint $table) {
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
            DB::statement('INSERT INTO `product_reviews_old` SELECT * FROM `product_reviews`');
            Schema::drop('product_reviews');
            Schema::rename('product_reviews_old', 'product_reviews');
            return;
        }

        DB::statement('ALTER TABLE `product_reviews` MODIFY `body` TEXT NOT NULL');
    }
};
