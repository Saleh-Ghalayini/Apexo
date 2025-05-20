<?php

namespace App\Services;

class ChatService
{
    protected \App\Services\ChatMessageService $chatMessageService;
    protected \App\Services\ChatReportService $chatReportService;
    protected \App\Services\ChatAiService $chatAiService;
    protected \App\Services\ToolDispatcherService $toolDispatcherService;
}
