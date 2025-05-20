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
}
