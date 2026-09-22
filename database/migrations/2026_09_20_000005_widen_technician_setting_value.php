<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Oude integer-waarden (20, 5) passen als string; nieuwe keys (none/percent) vereisen tekst.
    public function up(): void
    {
        Schema::table('technician_settings', function (Blueprint $table) {
            $table->string('value', 50)->change();
        });
    }

    public function down(): void
    {
        Schema::table('technician_settings', function (Blueprint $table) {
            $table->integer('value')->change();
        });
    }
};
