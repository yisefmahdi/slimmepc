<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laptop_loans', function (Blueprint $table) {
            $table->id();
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('phone');
            $table->string('address');
            $table->string('postcode');
            $table->string('city');
            $table->string('repair_number')->nullable();
            $table->string('laptop_type');
            $table->dateTime('given_at');
            $table->string('status')->default('uitgeleend');
            $table->string('pdf_path')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('customer_email');
            $table->index('created_at');
        });

        Schema::create('laptop_loan_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('laptop_loan_id')->constrained('laptop_loans')->cascadeOnDelete();
            $table->string('path');
            $table->string('original_name')->nullable();
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('size')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index('laptop_loan_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laptop_loan_photos');
        Schema::dropIfExists('laptop_loans');
    }
};
