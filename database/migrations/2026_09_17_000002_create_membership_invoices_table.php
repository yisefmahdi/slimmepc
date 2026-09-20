<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('membership_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('membership_id')->constrained('memberships')->onDelete('cascade');
            $table->string('klantnummer');
            $table->string('invoice_number')->unique();
            $table->date('invoice_date');
            $table->string('payment_method')->nullable();
            $table->decimal('subtotal', 10, 2);
            $table->decimal('tax_percentage', 5, 2)->default(21);
            $table->decimal('tax_amount', 10, 2);
            $table->decimal('total', 10, 2);
            $table->string('pdf_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('membership_invoices');
    }
};
