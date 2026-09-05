<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // coupon_usages depends on coupons and users, but carts not yet needed for FK
        // We create without cart FK first, add it after carts exists
        Schema::create('coupon_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coupon_id')->constrained('coupons')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->unsignedBigInteger('cart_id')->nullable();
            $table->string('guest_token', 64)->nullable();
            $table->dateTime('used_at')->nullable();
            $table->timestamps();

            $table->index('coupon_id');
            $table->index('guest_token');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coupon_usages');
    }
};
