<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// IDENTIEK aan oud project (2025_04_07_102756) + twee velden voor lidmaatschaps- en couponkorting.
// Alle oude kolommen ongewijzigd voor data-synchronisatie.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('technician_forms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('technician_id')->constrained('users')->onDelete('cascade');
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('duration_minutes');
            $table->integer('quarter_count');
            $table->decimal('quarter_price', 8, 2);
            $table->decimal('travel_cost', 8, 2);
            $table->decimal('subtotal', 10, 2);
            $table->decimal('btw', 10, 2);
            $table->decimal('total', 10, 2);
            $table->string('payment_status')->default('pending');
            $table->text('description')->nullable();
            $table->text('work_done')->nullable();
            $table->text('advice')->nullable();
            $table->unsignedTinyInteger('rating')->nullable();
            $table->text('comment')->nullable();
            // Nieuw (alleen nieuw project): toegepaste kortingen
            $table->decimal('member_discount', 10, 2)->default(0);
            $table->foreignId('coupon_id')->nullable()->constrained('coupons')->onDelete('set null');
            $table->decimal('coupon_discount', 10, 2)->default(0);
            $table->string('mollie_payment_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('technician_forms');
    }
};
