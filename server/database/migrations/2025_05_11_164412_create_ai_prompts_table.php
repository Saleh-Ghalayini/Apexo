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
        Schema::create('ai_prompts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('content'); // The actual prompt content
            $table->string('status')->default('pending'); // pending, processing, completed, failed
            $table->json('intent')->nullable(); // Extracted intent from Prism
            $table->json('parameters')->nullable(); // Extracted parameters from Prism
            $table->json('result')->nullable(); // Result of processing the prompt
            $table->text('error')->nullable(); // Error message if processing failed
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_prompts');
    }
};
