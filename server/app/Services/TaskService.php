<?php

namespace App\Services;

use App\Services\NotionService;

class TaskService
{
    private $NotionService;

    public function __construct(NotionService $NotionService)
    {
        $this->NotionService = $NotionService;
    }
    public function handleAIAction($user, $action, $data): array
    {
        switch ($action) {
            case 'create':
                return $this->NotionService->create($user, $data);
            case 'update':
                return $this->NotionService->update($user, $data);
            case 'assign':
                return $this->NotionService->assign($user, $data);
            case 'track':
                return $this->NotionService->track($user, $data);
            case 'delete':
                return $this->NotionService->delete($user, $data);
            default:
                return [
                    'status' => 'error',
                    'data' => [],
                    'message' => 'Unknown action provided.',
                ];
        }
    }
}
