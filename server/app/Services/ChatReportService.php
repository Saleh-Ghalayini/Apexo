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
}
