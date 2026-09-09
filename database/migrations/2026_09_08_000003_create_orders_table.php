<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('billing_address_id')->nullable()->constrained('addresses')->nullOnDelete();
            $table->foreignId('shipping_address_id')->nullable()->constrained('addresses')->nullOnDelete();
            $table->string('klantnummer')->nullable();
            $table->string('customer_email');
            $table->string('customer_phone');
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('tax_percentage', 5, 2)->default(21);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->string('discount_code')->nullable();
            $table->foreignId('coupon_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->string('shipping_method')->default('delivery');
            $table->decimal('shipping_cost', 10, 2)->default(0);
            $table->decimal('total_price', 10, 2)->default(0);
            $table->enum('payment_status', ['pending', 'paid', 'failed'])->default('pending');
            $table->string('payment_method')->nullable();
            $table->string('mollie_payment_id')->nullable()->unique();
            $table->enum('order_status', ['pending', 'processing', 'shipped', 'completed', 'cancelled'])->default('pending');
            $table->timestamps();

            $table->index('user_id');
            $table->index('customer_email');
            $table->index('payment_status');
            $table->index('order_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
