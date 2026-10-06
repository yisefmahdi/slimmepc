<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Protected digital delivery files (upload via admin "Bestanden").
     * Stored on the non-public `local` disk — served only through
     * DownloadController after an ownership/signature check.
     */
    public function up(): void
    {
        Schema::create('digital_files', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // original filename
            $table->string('path'); // storage path on the local disk
            $table->unsignedBigInteger('size');
            $table->string('mime', 128)->nullable();
            $table->foreignId('uploaded_by')->nullable()->nullOnDelete();
            $table->unsignedInteger('downloads_count')->default(0);
            $table->timestamps();

            $table->index('created_at');
        });

        Schema::create('digital_downloads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('digital_file_id')->constrained('digital_files')->cascadeOnDelete();
            $table->foreignId('order_id')->nullable()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->nullOnDelete();
            $table->string('ip', 45)->nullable();
            $table->timestamps();

            $table->index(['digital_file_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('digital_downloads');
        Schema::dropIfExists('digital_files');
    }
};
