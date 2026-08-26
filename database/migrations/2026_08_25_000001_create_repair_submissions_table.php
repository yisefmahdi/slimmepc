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
        Schema::create('repair_submissions', function (Blueprint $table) {
            $table->id();
            $table->string('repair_number')->unique();
            $table->string('device');
            $table->text('problems'); // JSON array of problem ids/labels
            $table->text('description');
            $table->string('brand');
            $table->string('model');
            $table->string('serial')->nullable();
            $table->string('data_importance');
            $table->string('opened_before');
            $table->string('name');
            $table->string('email');
            $table->string('phone');
            $table->string('postcode');
            $table->string('delivery_method');
            $table->string('contact_preference');
            $table->boolean('privacy')->default(false);
            $table->text('photos')->nullable(); // JSON array of filenames
            $table->enum('status', ['new', 'in_progress', 'completed'])->default('new');
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('email');
            $table->index('created_at');
            $table->index('repair_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('repair_submissions');
    }
};
