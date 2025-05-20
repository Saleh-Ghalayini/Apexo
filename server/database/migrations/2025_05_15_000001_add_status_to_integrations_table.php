<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('integrations', function (Blueprint $table) {
            if (!Schema::hasColumn('integrations', 'status')) {
                $table->string('status')->default('inactive')->after('provider');
            }
        });
    }

    public function down(): void
    {
        Schema::table('integrations', function (Blueprint $table) {
            if (Schema::hasColumn('integrations', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};
