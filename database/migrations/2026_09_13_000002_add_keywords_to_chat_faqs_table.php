<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('chat_faqs', function (Blueprint $table) {
            $table->text('keywords')->nullable()->after('answer')
                ->comment('Komma-gescheiden triggers (NL + AR), bv: telefoon,nummer,تواصل,اتصل');
        });
    }

    public function down(): void
    {
        Schema::table('chat_faqs', function (Blueprint $table) {
            $table->dropColumn('keywords');
        });
    }
};
