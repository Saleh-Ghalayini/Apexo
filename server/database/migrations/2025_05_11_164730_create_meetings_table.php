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
        Schema::create('meetings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->timestamp('scheduled_at');
            $table->timestamp('ended_at')->nullable();
            $table->text('transcript')->nullable();
            $table->text('summary')->nullable();
            $table->string('status')->default('scheduled'); // scheduled, in_progress, completed, canceled
            $table->string('external_id')->nullable(); // ID in third-party calendar system
            $table->string('meeting_url')->nullable(); // URL for the meeting (e.g., Zoom, Google Meet)
            $table->json('attendees')->nullable(); // List of attendees
            $table->json('metadata')->nullable(); // Additional metadata
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meetings');
    }
};
