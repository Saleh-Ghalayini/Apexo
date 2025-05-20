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
}
