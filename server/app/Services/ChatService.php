<?php

namespace App\Services;

class ChatService
{
    protected \App\Services\ChatMessageService $chatMessageService;
    protected \App\Services\ChatReportService $chatReportService;
    protected \App\Services\ChatAiService $chatAiService;
    protected \App\Services\ToolDispatcherService $toolDispatcherService;

    public function __construct(
        \App\Services\ChatMessageService $chatMessageService,
        \App\Services\ChatReportService $chatReportService,
        \App\Services\ChatAiService $chatAiService,
        \App\Services\ToolDispatcherService $toolDispatcherService
    ) {
        $this->chatMessageService = $chatMessageService;
        $this->chatReportService = $chatReportService;
        $this->chatAiService = $chatAiService;
        $this->toolDispatcherService = $toolDispatcherService;
    }

    public function startChatSession(array $participants): array
    {
        $session = [];
        $session['id'] = uniqid('chat_', true);
        $session['participants'] = $participants;
        return $session;
    }
}
