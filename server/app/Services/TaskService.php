<?php

namespace App\Services;

use App\Services\NotionService;

class TaskService
{
    private $notionService;

    public function __construct(NotionService $notionService)
    {
        $this->notionService = $notionService;
    }

    public function handleAIAction($user, $action, $data): array
    {
        switch ($action) {
            case 'create':
                return $this->notionService->create($user, $data);
            case 'update':
                return $this->notionService->update($user, $data);
            case 'assign':
                return $this->notionService->assign($user, $data);
            case 'track':
                return $this->notionService->track($user, $data);
            case 'delete':
                return $this->notionService->delete($user, $data);
            default:
                return [
                    'intent' => 'task_management',
                    'data' => [],
                    'message' => "Unknown or unsupported action: $action.",
                ];
        }
    }
}
