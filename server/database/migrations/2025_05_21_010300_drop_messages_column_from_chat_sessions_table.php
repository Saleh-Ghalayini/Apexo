<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropMessagesColumnFromChatSessionsTable extends Migration
{
    public function up()
    {
        Schema::table('chat_sessions', function (Blueprint $table) {
            if (Schema::hasColumn('chat_sessions', 'messages')) {
                $table->dropColumn('messages');
            }
        });
    }

    public function down()
    {
        Schema::table('chat_sessions', function (Blueprint $table) {
            $table->json('messages')->nullable(); // Adjust type if needed
        });
    }
}
