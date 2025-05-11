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
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Who created the report
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('type'); // 'task_summary', 'meeting_summary', 'productivity', etc.
            $table->string('title');
            $table->text('description')->nullable();
            $table->json('parameters')->nullable(); // Filters and options used to generate the report
            $table->json('data'); // The actual report data
            $table->timestamp('period_start')->nullable(); // Date range start, if applicable
            $table->timestamp('period_end')->nullable(); // Date range end, if applicable
            $table->boolean('is_scheduled')->default(false); // Whether this is a scheduled report
            $table->string('schedule')->nullable(); // Cron expression for scheduled reports
            $table->timestamp('last_generated_at'); // When the report was last generated
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
