<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->string('source_type')->nullable()->change();
            $table->unsignedBigInteger('source_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->string('source_type')->nullable(false)->change();
            $table->unsignedBigInteger('source_id')->nullable(false)->change();
        });
    }
};
