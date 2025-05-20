<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('integration_credentials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('integration_id')->constrained('integrations')->onDelete('cascade');
            $table->unsignedBigInteger('user_id'); // or company_id if multi-tenant by company
            $table->string('type'); // e.g., 'slack', 'notion', 'calendar'
            $table->string('access_token');
            $table->string('refresh_token')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unique(['integration_id', 'user_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('integration_credentials');
    }
};
