<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->timestamp('due_date')->nullable()->after('priority');
            $table->timestamp('completed_at')->nullable()->after('due_date');
            $table->string('assignee_name')->nullable()->after('completed_at');
            $table->string('assignee_email')->nullable()->after('assignee_name');
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn(['due_date', 'completed_at', 'assignee_name', 'assignee_email']);
        });
    }
};
