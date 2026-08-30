<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('device_receipts', function (Blueprint $table) {
            $table->id();
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('device_type');
            $table->string('phone_number');
            $table->string('serial_number')->nullable();
            $table->dateTime('received_at');
            $table->text('notes')->nullable();
            $table->string('type')->default('laptop');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('device_receipts');
    }
};
