<?php

namespace App\Services;

use App\Models\ChatMessage;
use App\Models\ChatSession;

class ChatMessageService
{
    public function persistUserMessage(ChatSession $session, string $userMessage): ChatMessage
    {
        $userChatMessage = new ChatMessage();
        $userChatMessage->chat_session_id = $session->id;
        $userChatMessage->role = 'user';
        $userChatMessage->content = $userMessage;
        $userChatMessage->save();
        return $userChatMessage;
    }
}
