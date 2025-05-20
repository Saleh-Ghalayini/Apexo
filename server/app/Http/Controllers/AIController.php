<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Meeting;
use App\Services\AIService;
use Illuminate\Http\Request;
use App\Models\EmployeeAnalytics;
use Illuminate\Support\Facades\Storage;
use App\Jobs\ProcessMeetingAnalyticsJob;
use App\Http\Requests\SendReportRequest;
use App\Jobs\ProcessEmployeeAnalyticsJob;
use App\Http\Requests\GenerateEmailRequest;

class AIController extends Controller
{
    protected $aiService;

    public function __construct(AIService $aiService)
    {
        $this->aiService = $aiService;
    }

    public function generateTaskReport()
    {
        $report = $this->aiService->generateTaskReport();
        return response()->json(['report' => $report]);
    }

    public function sendReport(SendReportRequest $request)
    {
        $validated = $request->validated();
        $sent = $this->aiService->sendReport($validated['report'], $validated['to'], $validated['from_user_id']);
        if ($sent) return response()->json(['message' => 'Report sent.']);

        return response()->json(['error' => 'Failed to send report.'], 500);
    }

    public function generateEmail(GenerateEmailRequest $request)
    {
        $data = $request->validated();
        $result = $this->aiService->generateTaskReminderEmail($data);
        if (!empty($result['success'])) return response()->json(['email' => $result['email']]);

        return response()->json(['error' => $result['error'] ?? 'Failed to generate email'], 500);
    }

    public function analyzeMeeting($meetingId)
    {
        $meeting = Meeting::findOrFail($meetingId);
        if (!$meeting->transcript) return response()->json(['error' => 'Meeting transcript is missing.'], 400);

        ProcessMeetingAnalyticsJob::dispatch($meeting->id);
        return response()->json(['message' => 'Meeting analytics job dispatched.']);
    }

    public function analyzeEmployee(Request $request, $employeeId)
    {
        $user = User::findOrFail($employeeId);
        $periodStart = $request->input('period_start');
        $periodEnd = $request->input('period_end');
        if (!$periodStart || !$periodEnd) return response()->json(['error' => 'period_start and period_end are required.'], 400);

        ProcessEmployeeAnalyticsJob::dispatch($user->id, $periodStart, $periodEnd);
        return response()->json(['message' => 'Employee analytics job dispatched.']);
    }

    public function getMeetingAnalytics($meetingId)
    {
        $meeting = Meeting::findOrFail($meetingId);
        return response()->json(['analytics' => $meeting->analytics]);
    }

    public function getEmployeeAnalytics(Request $request, $employeeId)
    {
        $periodStart = $request->input('period_start');
        $periodEnd = $request->input('period_end');
        $query = EmployeeAnalytics::where('user_id', $employeeId);
        if ($periodStart) $query->where('period_start', $periodStart);
        if ($periodEnd) $query->where('period_end', $periodEnd);
        $analytics = $query->latest()->first();
        return response()->json(['analytics' => $analytics ? $analytics->analytics : null]);
    }

    public function downloadMeetingReport(Request $request, $meetingId)
    {
        $format = $request->query('format', 'pdf');
        $meeting = Meeting::findOrFail($meetingId);
        if (!$meeting->analytics) return response()->json(['error' => 'No analytics available for this meeting.'], 404);

        if (!$meeting->report_file || $meeting->report_format !== $format || !Storage::exists($meeting->report_file))
            app(AIService::class)->generateMeetingReport($meeting, $format);

        $filename = basename($meeting->report_file);
        $mime = $format === 'pdf' ? 'application/pdf' : 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
        return Storage::download($meeting->report_file, $filename, ['Content-Type' => $mime]);
    }

    public function downloadEmployeeReport(Request $request, $employeeAnalyticsId)
    {
        $format = $request->query('format', 'pdf');
        $employeeAnalytics = EmployeeAnalytics::findOrFail($employeeAnalyticsId);
        if (!$employeeAnalytics->analytics)
            return response()->json(['error' => 'No analytics available for this employee/period.'], 404);

        if (!$employeeAnalytics->report_file || $employeeAnalytics->report_format !== $format || !Storage::exists($employeeAnalytics->report_file))
            app(AIService::class)->generateEmployeeReport($employeeAnalytics, $format);

        $filename = basename($employeeAnalytics->report_file);
        $mime = $format === 'pdf' ? 'application/pdf' : 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
        return Storage::download($employeeAnalytics->report_file, $filename, ['Content-Type' => $mime]);
    }
}
