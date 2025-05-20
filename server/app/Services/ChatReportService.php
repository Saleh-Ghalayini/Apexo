<?php

namespace App\Services;

use App\Models\ChatMessage;
use App\Models\ChatSession;

class ChatReportService
{
    protected $taskReportService;

    public function __construct(\App\Services\TaskReportService $taskReportService)
    {
        $this->taskReportService = $taskReportService;
    }

    public function isTaskReportRequest(string $userMessage): bool
    {
        $lowerMessage = strtolower($userMessage);
        return preg_match('/\b(generate|create|make) (a )?(task )?report\b/', $lowerMessage);
    }

    public function handleTaskReportRequest(ChatSession $session, ChatMessage $userChatMessage): array
    {
        $report = $this->taskReportService->generateTaskReport();
        return [
            'user_message' => $userChatMessage,
            'ai_message' => tap(new ChatMessage(), function ($aiChatMessage) use ($session, $report) {
                $aiChatMessage->chat_session_id = $session->id;
                $aiChatMessage->role = 'assistant';
                $aiChatMessage->content = $report;
                $aiChatMessage->save();
            }),
            'session' => $session,
        ];
    }
}
