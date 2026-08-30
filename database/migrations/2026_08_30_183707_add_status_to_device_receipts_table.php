<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('device_receipts', function (Blueprint $table) {
            $table->string('status')->default('completed')->after('type'); 
            // Defaults to 'completed' to handle existing data as requested
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('device_receipts', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
