<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// IDENTIEK aan oud project (2025_04_08_080217) — nodig voor data-synchronisatie.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('technician_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('technician_form_id')->constrained()->onDelete('cascade');
            $table->string('invoice_number')->unique();
            $table->date('invoice_date');
            $table->decimal('subtotal', 10, 2);
            $table->decimal('btw', 10, 2);
            $table->decimal('total', 10, 2);
            $table->enum('status', ['paid', 'unpaid'])->default('paid');
            $table->string('pdf_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('technician_invoices');
    }
};
