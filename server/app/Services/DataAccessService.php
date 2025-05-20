<?php

namespace App\Services;

use App\Models\User;
use App\Models\Task;
use App\Models\Meeting;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;

class DataAccessService
{
    public function getUserTasks(User $user, array $params = []): array
    {
        try {
            \Illuminate\Support\Facades\DB::enableQueryLog();

            $query = Task::query();

            if (isset($params['status']))
                $query->where('status', $params['status']);

            if (isset($params['due_date'])) {
                $date = $params['due_date'];
                if ($date === 'today')
                    $query->whereDate('deadline', Carbon::today());
                elseif ($date === 'week')
                    $query->whereBetween('deadline', [Carbon::now(), Carbon::now()->addWeek()]);
                elseif ($date === 'overdue')
                    $query->whereDate('deadline', '<', Carbon::today())
                        ->where('status', '!=', 'completed');
                else
                    $query->whereDate('deadline', $date);
            }

            if ($user->isEmployee())
                $query->where('assignee_id', $user->id);
            elseif ($user->isManager()) {
                $teamMemberIds = User::where('department', $user->department)->pluck('id')->toArray();
                $query->whereIn('assignee_id', $teamMemberIds);
            }

            $tasks = $query->orderBy('deadline', 'asc')->get();

            return [
                'success' => true,
                'data' => $tasks,
                'count' => $tasks->count()
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'Failed to retrieve tasks: ' . $e->getMessage(),
                'query' => \Illuminate\Support\Facades\DB::getQueryLog(),
            ];
        }
    }

    public function getUserMeetings(User $user, array $params = []): array
    {
        try {
            $query = Meeting::query();

            if (isset($params['date'])) {
                $date = $params['date'];
                if ($date === 'today')
                    $query->whereDate('scheduled_at', Carbon::today());
                elseif ($date === 'week')
                    $query->whereBetween('scheduled_at', [Carbon::now(), Carbon::now()->addWeek()]);
                else
                    $query->whereDate('scheduled_at', $date);
            }

            if ($user->isEmployee())
                $query->where(function ($q) use ($user) {
                    $q->where('user_id', $user->id)
                        ->orWhereJsonContains('attendees', [['id' => $user->id]]);
                });
            elseif ($user->isManager()) {
                $teamMemberIds = User::where('department', $user->department)->pluck('id')->toArray();
                $query->where(function ($q) use ($teamMemberIds, $user) {
                    $q->whereIn('user_id', $teamMemberIds)
                        ->orWhere(function ($q2) use ($teamMemberIds) {
                            foreach ($teamMemberIds as $memberId)
                                $q2->orWhereJsonContains('attendees', [['id' => $memberId]]);
                        });
                });
            }

            $meetings = $query->orderBy('scheduled_at', 'asc')->get();

            return [
                'success' => true,
                'data' => $meetings,
                'count' => $meetings->count()
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'Failed to retrieve meetings',
            ];
        }
    }

    public function getEmployeeInfo(User $user, string $employeeIdentifier): array
    {
        try {
            $query = User::query();

            if (is_numeric($employeeIdentifier))
                $query->where('id', $employeeIdentifier);
            else
                $query->where('name', 'like', "%{$employeeIdentifier}%");

            if ($user->isEmployee())
                $query->where('id', $user->id);
            elseif ($user->isManager())
                $query->where('department', $user->department);
            elseif ($user->isHR())
                $query->where('company_id', $user->company_id);

            if (!is_numeric($employeeIdentifier))
                $query->orderByRaw('CASE WHEN name = ? THEN 0 ELSE 1 END', [$employeeIdentifier]);

            $employee = $query->first();

            if (!$employee)
                return [
                    'success' => false,
                    'error' => 'Employee not found or access denied'
                ];

            $employeeData = $employee->toArray();

            if (!$user->isHR() && $user->id !== $employee->id) {
                $allowedFields = ['id', 'name', 'email', 'department', 'job_title', 'avatar'];
                $employeeData = array_intersect_key($employeeData, array_flip($allowedFields));
            }

            return [
                'success' => true,
                'data' => $employeeData
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'Failed to retrieve employee information'
            ];
        }
    }

    public function getDepartmentAnalytics(User $user, array $params = []): array
    {
        try {
            $period = $params['period'] ?? 'month';
            $currentDate = Carbon::now();

            switch ($period) {
                case 'week':
                    $startDate = $currentDate->copy()->startOfWeek();
                    $endDate = $currentDate->copy()->endOfWeek();
                    break;
                case 'month':
                    $startDate = $currentDate->copy()->startOfMonth();
                    $endDate = $currentDate->copy()->endOfMonth();
                    break;
                case 'quarter':
                    $startDate = $currentDate->copy()->startOfQuarter();
                    $endDate = $currentDate->copy()->endOfQuarter();
                    break;
                default:
                    $startDate = $currentDate->copy()->startOfMonth();
                    $endDate = $currentDate->copy()->endOfMonth();
            }

            if ($user->isEmployee())
                return [
                    'success' => false,
                    'error' => 'Access denied. Insufficient permissions.'
                ];

            $departments = [];
            if ($user->isManager())
                $departments = [$user->department];
            else {
                $departments = User::select('department')
                    ->distinct()
                    ->where('department', '!=', '')
                    ->whereNotNull('department')
                    ->pluck('department')
                    ->toArray();
            }

            $analytics = [];

            foreach ($departments as $department) {
                $totalTasks = Task::whereHas('user', function ($query) use ($department) {
                    $query->where('department', $department);
                })
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->count();

                $completedTasks = Task::whereHas('user', function ($query) use ($department) {
                    $query->where('department', $department);
                })
                    ->where('status', 'completed')
                    ->whereBetween('updated_at', [$startDate, $endDate])
                    ->count();

                $completionRate = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 2) : 0;

                $totalMeetings = Meeting::whereJsonContains('attendees', ['department' => $department])
                    ->whereBetween('scheduled_at', [$startDate, $endDate])
                    ->count();

                $attendedMeetings = Meeting::whereJsonContains('attendees', ['department' => $department])
                    ->whereJsonContains('attendees', ['status' => 'confirmed'])
                    ->whereBetween('scheduled_at', [$startDate, $endDate])
                    ->count();

                $attendanceRate = $totalMeetings > 0 ? round(($attendedMeetings / $totalMeetings) * 100, 2) : 0;

                $analytics[$department] = [
                    'task_completion_rate' => $completionRate,
                    'meeting_attendance_rate' => $attendanceRate,
                    'total_tasks' => $totalTasks,
                    'completed_tasks' => $completedTasks,
                    'total_meetings' => $totalMeetings,
                    'attended_meetings' => $attendedMeetings,
                    'period' => $period,
                    'date_range' => [
                        'start' => $startDate->toDateString(),
                        'end' => $endDate->toDateString()
                    ]
                ];
            }

            return [
                'success' => true,
                'data' => $analytics
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'Failed to retrieve analytics data'
            ];
        }
    }

    public function listCompanyEmployees(User $user): array
    {
        try {
            if (!$user->isHR())
                return [
                    'success' => false,
                    'error' => 'Access denied. Only HR users can list all employees.'
                ];

            $employees = User::where('company_id', $user->company_id)
                ->orderBy('name')
                ->get(['id', 'name', 'email', 'department', 'job_title', 'avatar', 'role']);
            return [
                'success' => true,
                'data' => $employees
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'Failed to retrieve employee list'
            ];
        }
    }

    public function createTask(User $user, array $data): array
    {
        if (!($user->isManager() || $user->isHR()))
            return [
                'success' => false,
                'error' => 'Access denied. Only managers and HR can create tasks.'
            ];

        try {
            $task = new Task();
            $task->user_id = $user->id;
            $task->assignee_id = $data['assignee_id'] ?? $user->id;
            $task->title = $data['title'] ?? '';
            $task->description = $data['description'] ?? null;
            $task->deadline = $data['deadline'] ?? null;
            $task->status = $data['status'] ?? 'pending';
            $task->priority = $data['priority'] ?? 'medium';
            $task->save();
            return [
                'success' => true,
                'data' => $task
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'Failed to create task'
            ];
        }
    }

    public function updateTaskStatus(User $user, int $taskId, string $status): array
    {
        try {
            $task = Task::find($taskId);
            if (!$task)
                return [
                    'success' => false,
                    'error' => 'Task not found.'
                ];

            if ($user->isEmployee() && $task->assignee_id !== $user->id)
                return [
                    'success' => false,
                    'error' => 'Access denied. Employees can only update their own tasks.'
                ];

            $task->status = $status;
            $task->save();
            return [
                'success' => true,
                'data' => $task
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'Failed to update task status'
            ];
        }
    }

    public function assignTask(User $user, int $taskId, int $assigneeId): array
    {
        if (!($user->isManager() || $user->isHR()))
            return [
                'success' => false,
                'error' => 'Access denied. Only managers and HR can assign tasks.'
            ];

        try {
            $task = Task::find($taskId);
            if (!$task)
                return [
                    'success' => false,
                    'error' => 'Task not found.'
                ];

            $task->assignee_id = $assigneeId;
            $task->save();
            return [
                'success' => true,
                'data' => $task
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'Failed to assign task'
            ];
        }
    }

    public function deleteTask(User $user, int $taskId): array
    {
        if (!($user->isManager() || $user->isHR()))
            return [
                'success' => false,
                'error' => 'Access denied. Only managers and HR can delete tasks.'
            ];

        try {
            $task = Task::find($taskId);
            if (!$task)
                return [
                    'success' => false,
                    'error' => 'Task not found.'
                ];

            $task->delete();
            return [
                'success' => true
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'Failed to delete task'
            ];
        }
    }

    public function generateReport(User $user, array $params): array
    {
        if (!($user->isHR() || $user->isManager()))
            return [
                'success' => false,
                'error' => 'Access denied. Only HR and managers can generate reports.'
            ];

        $type = $params['type'] ?? 'task_summary';
        $periodStart = $params['period_start'] ?? null;
        $periodEnd = $params['period_end'] ?? null;
        $filters = isset($params['filters']) ? json_decode($params['filters'], true) : [];

        if ($type === 'task_summary') {
            $query = Task::query();
            if ($periodStart) $query->whereDate('created_at', '>=', $periodStart);
            if ($periodEnd) $query->whereDate('created_at', '<=', $periodEnd);
            if (isset($filters['status'])) $query->where('status', $filters['status']);
            if ($user->isManager()) {
                $teamIds = User::where('department', $user->department)->pluck('id')->toArray();
                $query->whereIn('assignee_id', $teamIds);
            } elseif ($user->isHR()) {
                $query->whereHas('assignee', function ($q) use ($user) {
                    $q->where('company_id', $user->company_id);
                });
            }
            $tasks = $query->get();
            $summary = [
                'total' => $tasks->count(),
                'completed' => $tasks->where('status', 'completed')->count(),
                'in_progress' => $tasks->where('status', 'in_progress')->count(),
                'pending' => $tasks->where('status', 'pending')->count(),
                'tasks' => $tasks
            ];
            return [
                'success' => true,
                'type' => $type,
                'data' => $summary
            ];
        }

        return [
            'success' => false,
            'error' => 'Report type not implemented.'
        ];
    }

    public function saveReport(User $user, array $params): array
    {
        if (!($user->isHR() || $user->isManager()))
            return [
                'success' => false,
                'error' => 'Access denied. Only HR and managers can save reports.'
            ];

        try {
            $report = new \App\Models\Report();
            $report->user_id = $user->id;
            $report->company_id = $user->company_id;
            $report->type = $params['type'] ?? 'task_summary';
            $report->title = $params['title'] ?? '';
            $report->description = $params['description'] ?? null;
            $report->data = isset($params['data']) ? json_decode($params['data'], true) : [];
            $report->period_start = $params['period_start'] ?? null;
            $report->period_end = $params['period_end'] ?? null;
            $report->save();
            return [
                'success' => true,
                'report_id' => $report->id,
                'data' => $report
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'Failed to save report.'
            ];
        }
    }

    public function getReport(User $user, array $params): array
    {
        if (!($user->isHR() || $user->isManager()))
            return [
                'success' => false,
                'error' => 'Access denied. Only HR and managers can retrieve reports.'
            ];

        $query = \App\Models\Report::query();
        $query->where('company_id', $user->company_id);
        if (!empty($params['report_id'])) $query->where('id', $params['report_id']);
        if (!empty($params['type'])) $query->where('type', $params['type']);
        if (!empty($params['period_start'])) $query->whereDate('period_start', '>=', $params['period_start']);
        if (!empty($params['period_end'])) $query->whereDate('period_end', '<=', $params['period_end']);
        $reports = $query->orderByDesc('created_at')->get();
        return [
            'success' => true,
            'data' => $reports
        ];
    }

    public function emailReport(User $user, $reportId, $to): array
    {
        if (!($user->isHR() || $user->isManager()))
            return [
                'success' => false,
                'error' => 'Access denied. Only HR and managers can email reports.'
            ];

        $report = \App\Models\Report::where('company_id', $user->company_id)->find($reportId);
        if (!$report)
            return [
                'success' => false,
                'error' => 'Report not found.'
            ];

        try {
            $fromEmail = config('mail.from.address', 'noreply@apexo.com');
            Mail::raw(json_encode($report->data, JSON_PRETTY_PRINT), function ($message) use ($fromEmail, $to, $report) {
                $message->from($fromEmail)
                    ->to($to)
                    ->subject('Apexo Report: ' . $report->title);
            });
            return [
                'success' => true
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'Failed to email report.'
            ];
        }
    }

    public function announceMeetingWithEmail(User $user, array $params): array
    {
        if (!$user->isManager())
            return [
                'success' => false,
                'error' => 'Access denied. Only managers can announce meetings to their department.'
            ];

        $title = $params['title'] ?? 'Meeting';
        $scheduledAt = $params['scheduled_at'] ?? null;
        $description = $params['description'] ?? '';
        $meetingUrl = null;
        $calendarEvent = null;

        if ($user->google_calendar_token) {
            try {
                $calendarService = app(\App\Services\GoogleCalendarService::class);
                $calendarService->setAccessToken($user->google_calendar_token);
                $eventData = [
                    'summary' => $title,
                    'start' => ['dateTime' => $scheduledAt, 'timeZone' => config('app.timezone', 'UTC')],
                    'end' => ['dateTime' => $scheduledAt, 'timeZone' => config('app.timezone', 'UTC')], // For now, 1-hour default
                    'description' => $description,
                ];
                $calendarEvent = $calendarService->createEvent($eventData);
                if (isset($calendarEvent->hangoutLink)) {
                    $meetingUrl = $calendarEvent->hangoutLink;
                } elseif (isset($calendarEvent->conferenceData['entryPoints'][0]['uri'])) {
                    $meetingUrl = $calendarEvent->conferenceData['entryPoints'][0]['uri'];
                } elseif (isset($calendarEvent->htmlLink)) {
                    $meetingUrl = $calendarEvent->htmlLink;
                }
            } catch (\Throwable $e) {
            }
        }

        $meeting = new Meeting();
        $meeting->user_id = $user->id;
        $meeting->title = $title;
        $meeting->scheduled_at = $scheduledAt;
        $meeting->status = 'scheduled';
        $meeting->meeting_url = $meetingUrl;
        $meeting->description = $description;
        $meeting->attendees = [];
        $meeting->save();
        // Get all employees in department
        $employees = User::where('department', $user->department)->where('role', 'employee')->get();
        $emails = $employees->pluck('email')->filter()->all();
        // Compose email
        $subject = "[Apexo] New Meeting: $title";
        $body = "You are invited to a meeting.\n\nTitle: $title\nDate/Time: $scheduledAt\n";
        if ($description) $body .= "Description: $description\n";
        if ($meetingUrl) $body .= "Meeting Link: $meetingUrl\n";
        $body .= "\nThis meeting was scheduled by your manager via Apexo.";
        // Send email to all
        foreach ($emails as $to) {
            try {
                Mail::raw($body, function ($message) use ($to, $subject) {
                    $message->to($to)->subject($subject);
                });
            } catch (\Exception $e) {
            }
        }
        return [
            'success' => true,
            'meeting_id' => $meeting->id,
            'meeting_url' => $meetingUrl,
            'emails_sent' => count($emails)
        ];
    }

    public function sendEmail(User $user, $to, $subject, $body): array
    {
        \Illuminate\Support\Facades\Log::info('[DataAccessService] sendEmail called', [
            'user_id' => $user->id,
            'to' => $to,
            'subject' => $subject,
            'body' => $body
        ]);
        if (empty($to) || empty($subject) || empty($body)) {
            \Illuminate\Support\Facades\Log::warning('[DataAccessService] Missing required email fields', [
                'to' => $to,
                'subject' => $subject,
                'body' => $body
            ]);
            return [
                'success' => false,
                'error' => 'Missing required email fields.'
            ];
        }
        try {
            $fromEmail = config('mail.from.address', 'noreply@apexo.com');
            \Illuminate\Support\Facades\Log::info('[DataAccessService] Attempting to send email', [
                'from' => $fromEmail,
                'to' => $to,
                'subject' => $subject
            ]);
            Mail::raw($body, function ($message) use ($fromEmail, $to, $subject) {
                $message->from($fromEmail)
                    ->to($to)
                    ->subject($subject);
            });
            \Illuminate\Support\Facades\Log::info('[DataAccessService] Email sent successfully', [
                'to' => $to,
                'subject' => $subject
            ]);
            return [
                'success' => true
            ];
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('[DataAccessService] Error sending email: ' . $e->getMessage(), [
                'exception' => $e
            ]);
            return [
                'success' => false,
                'error' => 'Failed to send email.'
            ];
        }
    }
}
