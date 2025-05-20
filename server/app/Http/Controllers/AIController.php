<?php

namespace App\Http\Controllers;

use App\Services\AIService;
use App\Http\Requests\SendReportRequest;

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
}
