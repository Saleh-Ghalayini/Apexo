<?php

namespace App\Services;

class ToolDispatcherService
{
    protected array $tools;

    public function __construct(
        ChatReportService $chatReportService,
        ChatAiService $chatAiService,
        AIToolsService $aiToolsService,
        AIService $aiService
    ) {
        $this->tools = [
            'chat_report' => $chatReportService,
            'chat_ai' => $chatAiService,
            'ai_tools' => $aiToolsService,
            'ai_service' => $aiService,
        ];
    }

    public function dispatchTool($session, $user, $userMessage, $userChatMessage)
    {
        if ($this->tools['chat_report']->isTaskReportRequest($userMessage)) {
            Log::info('[ToolDispatcher] Task report request detected');
            return [
                'result' => $this->tools['chat_report']->handleTaskReportRequest($session, $userChatMessage),
                'tool' => 'task_report',
                'handled' => true,
            ];
        }
        if ($this->tools['chat_report']->isEmailReportRequest($userMessage)) {
            Log::info('[ToolDispatcher] Email report request detected');
            return [
                'result' => $this->tools['chat_report']->handleEmailReportRequest($session, $userChatMessage, $userMessage),
                'tool' => 'email_report',
                'handled' => true,
            ];
        }
    }
}
