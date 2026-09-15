<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('chat_faqs', function (Blueprint $table) {
            $table->json('embedding')->nullable()->after('keywords');
            $table->string('embedding_model', 100)->nullable()->after('embedding');
        });
    }

    public function down(): void
    {
        Schema::table('chat_faqs', function (Blueprint $table) {
            $table->dropColumn(['embedding', 'embedding_model']);
        });
    }
};
