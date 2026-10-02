<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Audit log for every admin push dispatch.
     */
    public function up(): void
    {
        Schema::create('admin_push_logs', function (Blueprint $table) {
            $table->id();
            $table->string('type', 60); // contact|repair|afspraak|chat|order|new_customer
            $table->string('ref_id', 100)->nullable();
            $table->string('title', 255);
            $table->string('url', 1024)->nullable();
            $table->unsignedInteger('targeted')->default(0);
            $table->unsignedInteger('delivered')->default(0);
            $table->text('response')->nullable();
            $table->timestamps();

            $table->index(['type', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_push_logs');
    }
};
