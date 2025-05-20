<?php

namespace App\Services;

use App\Services\Tools\ReportToolsService;
use App\Services\Tools\MeetingToolsService;
use App\Services\Tools\CalendarToolsService;
use App\Services\Tools\EmployeeToolsService;

class AIToolsService
{
    protected $dataAccessService;
    protected $meetingToolsService;
    protected $reportToolsService;
    protected $calendarToolsService;
    protected $employeeToolsService;

    public function __construct($dataAccessService)
    {
        $this->dataAccessService = $dataAccessService;
        $this->meetingToolsService = new MeetingToolsService($dataAccessService);
        $this->reportToolsService = new ReportToolsService($dataAccessService);
        $this->calendarToolsService = new CalendarToolsService($dataAccessService);
        $this->employeeToolsService = new EmployeeToolsService($dataAccessService);
    }

    public function getToolsForUser(\App\Models\User $user): array
    {
        $tools = [];
        $tools = array_merge(
            $tools,
            $this->meetingToolsService->getToolsForUser($user),
            $this->reportToolsService->getToolsForUser($user),
            $this->calendarToolsService->getToolsForUser($user),
            $this->employeeToolsService->getToolsForUser($user)
        );
        return $tools;
    }

    public function processToolCall(\App\Models\User $user, string $toolName, array $arguments): array
    {
        \Illuminate\Support\Facades\Log::info('[AIToolsService] processToolCall', [
            'user_id' => $user->id,
            'toolName' => $toolName,
            'arguments' => $arguments
        ]);

        switch ($toolName) {
            case 'get_user_tasks':
                return $this->dataAccessService->getUserTasks($user, $arguments);
            case 'get_user_meetings':
                return $this->dataAccessService->getUserMeetings($user, $arguments);
            case 'get_employee_info':
                return $this->dataAccessService->getEmployeeInfo($user, $arguments['employee_identifier'] ?? '');
            case 'get_department_analytics':
                if ($user->isEmployee())
                    return [
                        'success' => false,
                        'error' => 'Access denied. Insufficient permissions.'
                    ];
                return $this->dataAccessService->getDepartmentAnalytics($user, $arguments);
        }
    }
}
