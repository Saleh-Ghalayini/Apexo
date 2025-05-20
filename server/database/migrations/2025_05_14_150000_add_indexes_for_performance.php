<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Chat sessions: index user_id, status, created_at
        DB::statement('CREATE INDEX IF NOT EXISTS chat_sessions_user_id_index ON chat_sessions (user_id)');
        DB::statement('CREATE INDEX IF NOT EXISTS chat_sessions_status_index ON chat_sessions (status)');
        DB::statement('CREATE INDEX IF NOT EXISTS chat_sessions_created_at_index ON chat_sessions (created_at)');
        // Chat messages: index chat_session_id, created_at (user_id does NOT exist)
        DB::statement('CREATE INDEX IF NOT EXISTS chat_messages_chat_session_id_index ON chat_messages (chat_session_id)');
        DB::statement('CREATE INDEX IF NOT EXISTS chat_messages_created_at_index ON chat_messages (created_at)');
        // Announcements: index user_id, slack_channel, created_at
        DB::statement('CREATE INDEX IF NOT EXISTS announcements_user_id_index ON announcements (user_id)');
        DB::statement('CREATE INDEX IF NOT EXISTS announcements_slack_channel_index ON announcements (slack_channel)');
        DB::statement('CREATE INDEX IF NOT EXISTS announcements_created_at_index ON announcements (created_at)');
        // Integration credentials: index user_id, type
        DB::statement('CREATE INDEX IF NOT EXISTS integration_credentials_user_id_index ON integration_credentials (user_id)');
        DB::statement('CREATE INDEX IF NOT EXISTS integration_credentials_type_index ON integration_credentials (type)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop indexes if they exist
        DB::statement('DROP INDEX IF EXISTS chat_sessions_user_id_index');
        DB::statement('DROP INDEX IF EXISTS chat_sessions_status_index');
        DB::statement('DROP INDEX IF EXISTS chat_sessions_created_at_index');
        DB::statement('DROP INDEX IF EXISTS chat_messages_chat_session_id_index');
        DB::statement('DROP INDEX IF EXISTS chat_messages_created_at_index');
        DB::statement('DROP INDEX IF EXISTS announcements_user_id_index');
        DB::statement('DROP INDEX IF EXISTS announcements_slack_channel_index');
        DB::statement('DROP INDEX IF EXISTS announcements_created_at_index');
        DB::statement('DROP INDEX IF EXISTS integration_credentials_user_id_index');
        DB::statement('DROP INDEX IF EXISTS integration_credentials_type_index');
    }
};
