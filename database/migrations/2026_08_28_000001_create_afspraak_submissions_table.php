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
        Schema::create('afspraak_submissions', function (Blueprint $table) {
            $table->id();
            $table->string('afspraak_number')->unique();
            $table->string('name');
            $table->string('email');
            $table->string('street');
            $table->string('phone');
            $table->string('postcode');
            $table->string('house_number');
            $table->string('city');
            $table->string('device'); // plain text (no FK to CMS list)
            $table->text('problem');
            $table->date('preferred_date')->nullable();
            $table->string('preferred_time')->nullable();
            $table->enum('status', ['new', 'in_progress', 'completed'])->default('new');
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('email');
            $table->index('created_at');
            $table->index('afspraak_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('afspraak_submissions');
    }
};
