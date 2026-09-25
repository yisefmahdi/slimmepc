<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_sales_records', function (Blueprint $table) {
            $table->id();
            $table->string('product_name');
            $table->string('supplier_name');
            $table->date('purchase_date');
            $table->decimal('purchase_price', 10, 2);
            $table->string('customer_name')->nullable();
            $table->date('sale_date')->nullable();
            $table->decimal('sale_price', 10, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_sales_records');
    }
};
