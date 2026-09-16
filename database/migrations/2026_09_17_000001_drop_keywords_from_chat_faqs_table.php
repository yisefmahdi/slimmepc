<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * De chat-agent werkt puur semantisch (embeddings) — de
     * keyword-kolom is nergens meer voor nodig.
     */
    public function up(): void
    {
        Schema::table('chat_faqs', function (Blueprint $table) {
            $table->dropColumn('keywords');
        });
    }

    public function down(): void
    {
        Schema::table('chat_faqs', function (Blueprint $table) {
            $table->text('keywords')->nullable()->after('answer');
        });
    }
};
