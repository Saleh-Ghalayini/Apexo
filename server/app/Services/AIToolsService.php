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
            case 'list_company_employees':
                return $this->dataAccessService->listCompanyEmployees($user);
            case 'create_google_calendar_event': {
                    $event = $arguments['event'] ?? [];
                    $token = $user->google_calendar_token;
                    if (!$token)    return ['success' => false, 'error' => 'Google Calendar not connected'];

                    try {
                        $calendarService = app(\App\Services\GoogleCalendarService::class);
                        $calendarService->setAccessToken($token);

                        $userTimezone = $event['timeZone'] ?? 'Asia/Beirut';
                        $eventData = [
                            'summary' => $event['summary'] ?? '',
                            'start' => ['dateTime' => $event['start'] ?? '', 'timeZone' => $userTimezone],
                            'end' => ['dateTime' => $event['end'] ?? '', 'timeZone' => $userTimezone],
                        ];
                        if (!empty($event['description']))
                            $eventData['description'] = $event['description'];
                        if (!empty($event['attendees'])) {
                            $emails = array_filter(array_map('trim', explode(',', $event['attendees'])), function ($email) {
                                return filter_var($email, FILTER_VALIDATE_EMAIL);
                            });
                            if (!empty($emails))
                                $eventData['attendees'] = array_map(function ($email) {
                                    return ['email' => $email];
                                }, $emails);
                        }
                        $created = $calendarService->createEvent($eventData);
                        return ['success' => true, 'event' => $created];
                    } catch (\Throwable $e) {
                        return ['success' => false, 'error' => 'Exception: ' . $e->getMessage()];
                    }
                }
            case 'list_google_calendar_events': {
                    $params = $arguments;
                    $token = $user->google_calendar_token;
                    if (!$token)    return ['success' => false, 'error' => 'Google Calendar not connected'];

                    try {
                        $calendarService = app(\App\Services\GoogleCalendarService::class);
                        $calendarService->setAccessToken($token);
                        $maxResults = isset($params['maxResults']) ? (int)$params['maxResults'] : 10;
                        $events = $calendarService->listUpcomingEvents($maxResults);
                        return ['success' => true, 'events' => $events];
                    } catch (\Throwable $e) {
                        return ['success' => false, 'error' => 'Exception: ' . $e->getMessage()];
                    }
                }
            case 'generate_report':
                return $this->dataAccessService->generateReport($user, $arguments);
            case 'save_report':
                return $this->dataAccessService->saveReport($user, $arguments);
            case 'get_report':
                return $this->dataAccessService->getReport($user, $arguments);
            case 'email_report':
                return $this->dataAccessService->emailReport($user, $arguments['report_id'] ?? null, $arguments['to'] ?? null);
            case 'announce_meeting_with_email':
                return $this->dataAccessService->announceMeetingWithEmail($user, $arguments);
            case 'send_email':
                $params = $arguments['params'] ?? $arguments;
                return $this->dataAccessService->sendEmail(
                    $user,
                    $params['to'] ?? null,
                    $params['subject'] ?? '',
                    $params['body'] ?? ''
                );
        }
    }
}
