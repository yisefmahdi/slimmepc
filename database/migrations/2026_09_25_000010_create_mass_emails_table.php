<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mass_emails', function (Blueprint $table) {
            $table->id();
            $table->string('message_type');
            $table->text('message_content');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mass_emails');
    }
};
