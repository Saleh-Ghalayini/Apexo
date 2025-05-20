<?php

namespace App\Services;

use App\Models\Task;
use App\Models\Meeting;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\EmployeeAnalytics;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class AIService
{
    public function generateTaskReport(): string
    {
        $tasks = Task::with(['user', 'assignee'])->get();
        $total = $tasks->count();
        $completed = $tasks->where('status', 'completed')->count();
        $inProgress = $tasks->where('status', 'in_progress')->count();
        $pending = $tasks->where('status', 'pending')->count();

        $report = "TASK REPORT\n";
        $report .= "1. Overview:\n";
        $report .= "Total Tasks: $total\n";
        $report .= "Completed Tasks: $completed\n";
        $report .= "In Progress Tasks: $inProgress\n";
        $report .= "Pending Tasks: $pending\n";
        $report .= "2. Task Breakdown by Status:\n";

        $statuses = ['completed', 'in_progress', 'pending'];
        foreach ($statuses as $status) {
            $report .= ucfirst(str_replace('_', ' ', $status)) . " Tasks:\n";
            foreach ($tasks->where('status', $status) as $task) {
                $report .= "- {$task->title}\n";
                $report .= "- Deadline: {$task->deadline?->format('Y-m-d')}\n";
                $report .= "- Priority: " . ucfirst($task->priority) . "\n";
            }
        }

        $priorityCounts = [
            'High' => $tasks->where('priority', 'high')->count(),
            'Medium' => $tasks->where('priority', 'medium')->count(),
            'Low' => $tasks->where('priority', 'low')->count(),
        ];

        $report .= "3. Prioritization Summary:\n";
        foreach ($priorityCounts as $priority => $count)
            $report .= "$priority Priority: $count tasks\n";

        return $report;
    }

    public function sendReport(string $report, string $to): bool
    {
        $fromEmail = 'ghalayinisaleh9@gmail.com';
        try {
            Mail::raw($report, function ($message) use ($fromEmail, $to) {
                $message->from($fromEmail)
                    ->to($to)
                    ->subject('Task Report');
            });
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function generateTaskReminderEmail(array $data): array
    {
        $prompt = "Generate a friendly and professional email reminder for the following user and task.\n" .
            "User Name: {$data['user_name']}\n" .
            "User Email: {$data['user_email']}\n" .
            "Task Title: {$data['task_title']}\n" .
            "Task Details: {$data['task_details']}\n" .
            "Deadline: {$data['deadline']}\n" .
            "Return a JSON object with 'subject' and 'body'.";

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . config('services.openai.secret'),
        ])->post(config('services.openai.url', 'https://api.openai.com/v1') . '/chat/completions', [
            'model' => config('services.openai.model', 'gpt-4o'),
            'messages' => [
                ['role' => 'system', 'content' => 'You are an assistant that writes professional emails.'],
                ['role' => 'user', 'content' => $prompt],
            ],
            'max_tokens' => 400,
            'temperature' => config('services.openai.temperature', 0.7),
        ]);

        if ($response->failed())
            return [
                'success' => false,
                'error' => 'AI API failed',
            ];

        $choices = $response->json('choices');
        $content = $choices[0]['message']['content'] ?? '';

        $json = null;
        if (preg_match('/```json(.*?)```/s', $content, $matches))
            $json = trim($matches[1]);
        elseif (preg_match('/\{.*\}/s', $content, $matches))
            $json = $matches[0];

        $email = json_decode($json, true);
        if (!$email || !isset($email['subject']) || !isset($email['body']))
            return [
                'success' => false,
                'error' => 'AI did not return a valid email format',
            ];

        $email = json_decode($json, true);
        if (!$email || !isset($email['subject']) || !isset($email['body']))
            return [
                'success' => false,
                'error' => 'AI did not return a valid email format',
            ];

        return [
            'success' => true,
            'email' => $email,
        ];
    }

    public function analyzeMeetingTranscript(string $transcript, array $meetingData = []): array
    {
        $prompt = "Analyze the following meeting transcript. Provide a summary, sentiment, action items (with assignee and due date if possible), and main topics discussed. Return a JSON object with keys: summary, sentiment, action_items (array), topics (array).\nTranscript:\n$transcript";

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . config('services.openai.secret'),
        ])->post(config('services.openai.url', 'https://api.openai.com/v1') . '/chat/completions', [
            'model' => config('services.openai.model', 'gpt-4o'),
            'messages' => [
                ['role' => 'system', 'content' => 'You are an assistant that analyzes meeting transcripts and returns structured analytics.'],
                ['role' => 'user', 'content' => $prompt],
            ],
            'max_tokens' => 800,
            'temperature' => config('services.openai.temperature', 0.4),
        ]);

        if ($response->failed())
            return [
                'success' => false,
                'error' => 'AI API failed',
            ];

        $choices = $response->json('choices');
        $content = $choices[0]['message']['content'] ?? '';

        $json = null;
        if (preg_match('/```json(.*?)```/s', $content, $matches))
            $json = trim($matches[1]);
        elseif (preg_match('/\{.*\}/s', $content, $matches))
            $json = $matches[0];

        $analytics = json_decode($json, true);
        if (!$analytics || !isset($analytics['summary']))
            return [
                'success' => false,
                'error' => 'AI did not return valid analytics',
            ];

        $analytics = json_decode($json, true);
        if (!$analytics || !isset($analytics['summary']))
            return [
                'success' => false,
                'error' => 'AI did not return valid analytics',
            ];

        $analytics['success'] = true;
        return $analytics;
    }

    public function analyzeEmployeePerformance($user, $meetings, $tasks, $periodStart, $periodEnd): array
    {
        $meetingCount = $meetings->count();
        $taskCount = $tasks->count();
        $completedTasks = $tasks->where('status', 'completed')->count();
        $meetingTitles = $meetings->pluck('title')->implode(', ');
        $taskTitles = $tasks->pluck('title')->implode(', ');
        $prompt = "Analyze the following employee's performance for the period $periodStart to $periodEnd.\n" .
            "Employee: {$user->name}\n" .
            "Meetings attended ($meetingCount): $meetingTitles\n" .
            "Tasks assigned ($taskCount): $taskTitles\n" .
            "Tasks completed: $completedTasks\n" .
            "Provide a summary, notable achievements, areas for improvement, and overall sentiment. Return a JSON object with keys: summary, meetings_attended, tasks_completed, tasks_assigned, sentiment, notable_achievements (array), areas_for_improvement (array).";

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . config('services.openai.secret'),
        ])->post(config('services.openai.url', 'https://api.openai.com/v1') . '/chat/completions', [
            'model' => config('services.openai.model', 'gpt-4o'),
            'messages' => [
                ['role' => 'system', 'content' => 'You are an assistant that analyzes employee performance and returns structured analytics.'],
                ['role' => 'user', 'content' => $prompt],
            ],
            'max_tokens' => 800,
            'temperature' => config('services.openai.temperature', 0.4),
        ]);

        if ($response->failed())
            return [
                'success' => false,
                'error' => 'AI API failed',
            ];

        $choices = $response->json('choices');
        $content = $choices[0]['message']['content'] ?? '';

        $json = null;
        if (preg_match('/```json(.*?)```/s', $content, $matches))   $json = trim($matches[1]);
        elseif (preg_match('/\{.*\}/s', $content, $matches))    $json = $matches[0];

        $analytics = json_decode($json, true);
        if (!$analytics || !isset($analytics['summary']))
            return [
                'success' => false,
                'error' => 'AI did not return valid analytics',
            ];

        $analytics = json_decode($json, true);
        if (!$analytics || !isset($analytics['summary']))
            return [
                'success' => false,
                'error' => 'AI did not return valid analytics',
            ];

        $analytics['success'] = true;
        return $analytics;
    }

    public function generateMeetingReport(Meeting $meeting, string $format = 'pdf'): string
    {
        $meeting = Meeting::find($meeting->id);
        $analytics = $meeting->analytics;
        if (!is_array($analytics) || empty($analytics) || !isset($analytics['summary'])) {
            $analytics = [
                'summary' => $meeting->summary,
                'sentiment' => $meeting->metadata['sentiment'] ?? null,
                'topics' => $meeting->metadata['topics'] ?? null,
                'action_items' => $meeting->metadata['action_items'] ?? null,
            ];
        }

        $filename = 'meeting_report_' . $meeting->id . '_' . now()->timestamp . '.' . $format;
        $path = 'reports/' . $filename;

        if ($format === 'pdf') {
            $pdf = Pdf::loadView('reports.meeting', ['meeting' => $meeting, 'analytics' => $analytics]);
            Storage::put($path, $pdf->output());
        } elseif ($format === 'xlsx' || $format === 'excel')
            Excel::store(new \App\Exports\MeetingReportExport($meeting, $analytics), $path);
        else
            throw new \Exception('Unsupported format');

        $meeting->report_file = $path;
        $meeting->report_format = $format;
        $meeting->save();

        return $path;
    }

    public function generateEmployeeReport(EmployeeAnalytics $employeeAnalytics, string $format = 'pdf'): string
    {
        $user = $employeeAnalytics->user;
        $analytics = $employeeAnalytics->analytics ?? [];

        $filename = 'employee_report_' . $user->id . '_' . now()->timestamp . '.' . $format;
        $path = 'reports/' . $filename;
    }
}
