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
        Schema::create('integration_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('integration_id')->constrained()->onDelete('cascade');
            $table->string('data_type'); // 'notion_database', 'notion_page', 'slack_channel', etc.
            $table->string('external_id'); // ID in the external system
            $table->string('name'); // Human readable name
            $table->text('description')->nullable();
            $table->json('data'); // Additional data specific to the integration type
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['integration_id', 'data_type', 'external_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('integration_data');
    }
};
