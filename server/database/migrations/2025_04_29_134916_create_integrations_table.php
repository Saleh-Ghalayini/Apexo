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
        if (!Schema::hasTable('integrations')) {
            Schema::create('integrations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_id')->nullable()->constrained()->onDelete('cascade');
                $table->string('name')->nullable();
                $table->string('email')->nullable();
                $table->string('provider')->nullable();
                $table->string('status')->default('inactive');
                $table->jsonb('settings')->nullable();
                $table->timestamp('connected_at')->nullable();
                $table->timestamp('disconnected_at')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('integrations');
    }
};
