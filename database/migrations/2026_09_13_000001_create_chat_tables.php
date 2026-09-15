<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chat_faqs', function (Blueprint $table) {
            $table->id();
            $table->string('question');
            $table->text('answer');
            $table->string('category')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->index('is_active');
        });

        Schema::create('chat_availability', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('day_of_week')->unique()->comment('0=Zondag .. 6=Zaterdag');
            $table->boolean('is_open')->default(true);
            $table->time('open_at')->nullable();
            $table->time('close_at')->nullable();
            $table->timestamps();
        });

        // Standaard: ma–vr 09:00–17:00, za 10:00–14:00, zo gesloten.
        $defaults = [
            ['day_of_week' => 0, 'is_open' => false, 'open_at' => null, 'close_at' => null],
            ['day_of_week' => 1, 'is_open' => true, 'open_at' => '09:00', 'close_at' => '17:00'],
            ['day_of_week' => 2, 'is_open' => true, 'open_at' => '09:00', 'close_at' => '17:00'],
            ['day_of_week' => 3, 'is_open' => true, 'open_at' => '09:00', 'close_at' => '17:00'],
            ['day_of_week' => 4, 'is_open' => true, 'open_at' => '09:00', 'close_at' => '17:00'],
            ['day_of_week' => 5, 'is_open' => true, 'open_at' => '09:00', 'close_at' => '17:00'],
            ['day_of_week' => 6, 'is_open' => true, 'open_at' => '10:00', 'close_at' => '14:00'],
        ];
        foreach ($defaults as $row) {
            DB::table('chat_availability')->updateOrInsert(
                ['day_of_week' => $row['day_of_week']],
                $row + ['created_at' => now(), 'updated_at' => now()]
            );
        }

        Schema::create('chat_closed_dates', function (Blueprint $table) {
            $table->id();
            $table->date('closed_at')->unique();
            $table->string('reason')->nullable();
            $table->timestamps();
        });

        Schema::create('chat_conversations', function (Blueprint $table) {
            $table->id();
            $table->string('guest_token', 64)->unique();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('name');
            $table->string('email');
            $table->string('status', 20)->default('ai')->comment('ai,open,handed_over,closed,offline');
            $table->boolean('ai_enabled')->default(true);
            $table->timestamp('handed_over_at')->nullable();
            $table->timestamp('admin_read_at')->nullable();
            $table->timestamp('last_activity_at')->nullable();
            $table->unsignedTinyInteger('rating')->nullable();
            $table->text('rating_comment')->nullable();
            $table->timestamp('rated_at')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
            $table->index(['status', 'last_activity_at']);
            $table->index('user_id');
        });

        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chat_conversation_id')->constrained('chat_conversations')->cascadeOnDelete();
            $table->string('sender', 20)->default('customer')->comment('customer,admin,ai');
            $table->text('body')->nullable();
            $table->string('attachment')->nullable();
            $table->string('source', 20)->default('widget')->comment('widget,dashboard,email,ai');
            $table->timestamps();
            $table->index('chat_conversation_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_messages');
        Schema::dropIfExists('chat_conversations');
        Schema::dropIfExists('chat_closed_dates');
        Schema::dropIfExists('chat_availability');
        Schema::dropIfExists('chat_faqs');
    }
};
