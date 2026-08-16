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
        Schema::create('contact_replies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contact_submission_id')->constrained()->cascadeOnDelete();
            $table->enum('sender', ['customer', 'admin'])->default('customer');
            $table->text('body');
            $table->string('attachment')->nullable();
            $table->enum('source', ['dashboard', 'email', 'inbound'])->default('dashboard');
            $table->timestamps();

            $table->index('contact_submission_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contact_replies');
    }
};