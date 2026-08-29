<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('manual_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('invoice_number')->unique();
            $table->string('device_info')->nullable();
            $table->text('description')->nullable();
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->integer('tax_percentage')->default(21);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            $table->string('pdf_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('manual_invoices');
    }
};
