<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('integrations', function (Blueprint $table) {
            if (!Schema::hasColumn('integrations', 'token_type')) {
                $table->string('token_type')->nullable()->after('status');
            }
            if (!Schema::hasColumn('integrations', 'access_token')) {
                $table->string('access_token', 512)->nullable()->after('token_type');
            }
            if (!Schema::hasColumn('integrations', 'refresh_token')) {
                $table->string('refresh_token', 512)->nullable()->after('access_token');
            }
            if (!Schema::hasColumn('integrations', 'expires_at')) {
                $table->timestamp('expires_at')->nullable()->after('refresh_token');
            }
            if (!Schema::hasColumn('integrations', 'metadata')) {
                $table->json('metadata')->nullable()->after('expires_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('integrations', function (Blueprint $table) {
            if (Schema::hasColumn('integrations', 'token_type')) {
                $table->dropColumn('token_type');
            }
            if (Schema::hasColumn('integrations', 'access_token')) {
                $table->dropColumn('access_token');
            }
            if (Schema::hasColumn('integrations', 'refresh_token')) {
                $table->dropColumn('refresh_token');
            }
            if (Schema::hasColumn('integrations', 'expires_at')) {
                $table->dropColumn('expires_at');
            }
            if (Schema::hasColumn('integrations', 'metadata')) {
                $table->dropColumn('metadata');
            }
        });
    }
};
