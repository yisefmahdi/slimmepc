<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Make "model" nullable: the form field is optional and
     * ConvertEmptyStringsToNull turns "" into null on submit.
     */
    public function up(): void
    {
        Schema::table('repair_submissions', function (Blueprint $table) {
            $table->string('model')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('repair_submissions', function (Blueprint $table) {
            $table->string('model')->nullable(false)->change();
        });
    }
};
