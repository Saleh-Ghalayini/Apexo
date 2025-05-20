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

        $toolMap = [
            'get_user_tasks' => fn() => $this->dataAccessService->getUserTasks($user, $arguments),
            'get_user_meetings' => fn() => $this->dataAccessService->getUserMeetings($user, $arguments),
            'get_employee_info' => fn() => $this->dataAccessService->getEmployeeInfo($user, $arguments['employee_identifier'] ?? ''),
            'get_department_analytics' => fn() => $this->handleDepartmentAnalytics($user, $arguments),
            'list_company_employees' => fn() => $this->dataAccessService->listCompanyEmployees($user),
            'create_google_calendar_event' => fn() => $this->handleCreateGoogleCalendarEvent($user, $arguments),
            'list_google_calendar_events' => fn() => $this->handleListGoogleCalendarEvents($user, $arguments),
            'generate_report' => fn() => $this->dataAccessService->generateReport($user, $arguments),
            'save_report' => fn() => $this->dataAccessService->saveReport($user, $arguments),
            'get_report' => fn() => $this->dataAccessService->getReport($user, $arguments),
            'email_report' => fn() => $this->dataAccessService->emailReport($user, $arguments['report_id'] ?? null, $arguments['to'] ?? null),
            'announce_meeting_with_email' => fn() => $this->dataAccessService->announceMeetingWithEmail($user, $arguments),
            'send_email' => fn() => $this->handleSendEmail($user, $arguments),
            'create_meeting' => fn() => $this->handleCreateMeeting($user, $arguments),
            'trigger_meeting_analytics' => fn() => $this->handleTriggerMeetingAnalytics($arguments),
            'download_meeting_report' => fn() => $this->handleDownloadMeetingReport($arguments),
        ];

        if (isset($toolMap[$toolName])) {
            try {
                return $toolMap[$toolName]();
            } catch (\Throwable $e) {
                return ['success' => false, 'error' => 'Exception: ' . $e->getMessage()];
            }
        }

        return [
            'success' => false,
            'error' => 'Unknown tool: ' . $toolName
        ];
    }

    private function handleDepartmentAnalytics($user, $arguments)
    {
        if ($user->isEmployee()) {
            return [
                'success' => false,
                'error' => 'Access denied. Insufficient permissions.'
            ];
        }
        return $this->dataAccessService->getDepartmentAnalytics($user, $arguments);
    }

    private function handleCreateGoogleCalendarEvent($user, $arguments)
    {
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

    private function handleListGoogleCalendarEvents($user, $arguments)
    {
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

    private function handleSendEmail($user, $arguments)
    {
        $params = $arguments['params'] ?? $arguments;
        return $this->dataAccessService->sendEmail(
            $user,
            $params['to'] ?? null,
            $params['subject'] ?? '',
            $params['body'] ?? ''
        );
    }

    private function handleCreateMeeting($user, $arguments)
    {
        $meetingData = $arguments['params'] ?? $arguments;
        if (isset($meetingData['attendees']) && is_string($meetingData['attendees']))
            $meetingData['attendees'] = array_map('trim', explode(',', $meetingData['attendees']));
        if (isset($meetingData['attendees']) && is_array($meetingData['attendees'])) {
            $meetingData['attendees'] = array_map(function ($attendee) {
                if (filter_var($attendee, FILTER_VALIDATE_EMAIL))   return ['email' => $attendee];
                else  return ['name' => $attendee];
            }, $meetingData['attendees']);
        }
        $meetingData['user_id'] = $user->id;
        $existing = \App\Models\Meeting::where('user_id', $meetingData['user_id'])
            ->where('title', $meetingData['title'])
            ->where('scheduled_at', $meetingData['scheduled_at'])
            ->first();
        if ($existing)  return ['success' => true, 'meeting_id' => $existing->id, 'duplicate' => true];
        $meeting = \App\Models\Meeting::create($meetingData);
        if (!empty($meetingData['transcript'])) {
            $aiService = app(\App\Services\AIService::class);
            $analytics = $aiService->analyzeMeetingTranscript($meetingData['transcript'], $meeting->toArray());
            if (isset($analytics['summary']))   $meeting->summary = $analytics['summary'];
            $meeting->analytics = $analytics;
            $meeting->save();
        }
        return ['success' => true, 'meeting_id' => $meeting->id];
    }

    private function handleTriggerMeetingAnalytics($arguments)
    {
        $meetingId = $arguments['meeting_id'] ?? null;
        \App\Jobs\ProcessMeetingAnalyticsJob::dispatch($meetingId);
        return ['success' => true, 'message' => 'Meeting analytics job dispatched.'];
    }

    private function handleDownloadMeetingReport($arguments)
    {
        $meetingId = $arguments['meeting_id'] ?? null;
        $format = $arguments['format'] ?? 'pdf';
        $meeting = \App\Models\Meeting::findOrFail($meetingId);
        if (!$meeting->report_file || $meeting->report_format !== $format || !\Illuminate\Support\Facades\Storage::exists($meeting->report_file))
            app(\App\Services\AIService::class)->generateMeetingReport($meeting, $format);
        $filePath = $meeting->report_file;
        $filename = basename($filePath);
        $mime = $format === 'pdf' ? 'application/pdf' : 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
        $fileContent = \Illuminate\Support\Facades\Storage::get($filePath);
        $base64 = base64_encode($fileContent);
        return ['success' => true, 'file' => [
            'name' => $filename,
            'mime' => $mime,
            'base64' => $base64,
        ]];
    }
}
