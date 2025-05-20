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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('assignee_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('title');
            $table->text('description')->nullable();
            $table->timestamp('deadline')->nullable();
            $table->string('status')->default('pending'); // pending, in_progress, completed, canceled
            $table->string('priority')->default('medium'); // low, medium, high

            // Polymorphic relationship for source (Meeting or AiPrompt)
            $table->morphs('source');

            // External integration info
            $table->string('external_id')->nullable(); // ID in third-party system
            $table->string('external_url')->nullable(); // URL in third-party system
            $table->string('external_system')->nullable(); // notion, slack, etc.
            $table->json('external_data')->nullable(); // Additional data from external system

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
