<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('memberships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('klantnummer')->unique();
            $table->enum('customer_type', ['private', 'business'])->default('private');
            $table->enum('customer_gender', ['man', 'vrouw'])->nullable();
            $table->string('name');
            $table->string('customer_email');
            $table->string('customer_phone')->nullable();
            $table->string('customer_address')->nullable();
            $table->string('postcode')->nullable();
            $table->string('city')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('total', 10, 2)->nullable();
            $table->enum('payment_status', ['paid', 'unpaid', 'cancelled'])->default('unpaid');
            $table->string('payment_method')->nullable();
            $table->string('mollie_payment_id')->nullable();
            $table->boolean('terms_accepted')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('memberships');
    }
};
