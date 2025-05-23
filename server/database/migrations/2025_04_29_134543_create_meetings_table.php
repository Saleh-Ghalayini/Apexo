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
        if (!Schema::hasTable('meetings')) {
            Schema::create('meetings', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->string('title');
                $table->timestamp('scheduled_at');
                $table->timestamp('ended_at')->nullable();
                $table->text('transcript')->nullable();
                $table->text('summary')->nullable();
                $table->string('status')->default('scheduled');
                $table->string('external_id')->nullable();
                $table->string('meeting_url')->nullable();
                $table->json('attendees')->nullable();
                $table->json('metadata')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meetings');
    }
};
