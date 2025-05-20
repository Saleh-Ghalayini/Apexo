<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_analytics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->date('period_start');
            $table->date('period_end');
            $table->json('analytics')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'period_start', 'period_end'], 'employee_period_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_analytics');
    }
};
