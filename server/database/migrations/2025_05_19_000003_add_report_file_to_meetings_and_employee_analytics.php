<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('meetings', function (Blueprint $table) {
            $table->string('report_file')->nullable()->after('analytics');
            $table->string('report_format')->nullable()->after('report_file');
        });
        Schema::table('employee_analytics', function (Blueprint $table) {
            $table->string('report_file')->nullable()->after('analytics');
            $table->string('report_format')->nullable()->after('report_file');
        });
    }

    public function down(): void
    {
        Schema::table('meetings', function (Blueprint $table) {
            $table->dropColumn(['report_file', 'report_format']);
        });
        Schema::table('employee_analytics', function (Blueprint $table) {
            $table->dropColumn(['report_file', 'report_format']);
        });
    }
};
