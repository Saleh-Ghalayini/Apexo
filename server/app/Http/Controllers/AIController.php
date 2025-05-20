<?php

namespace App\Http\Controllers;

use App\Services\AIService;

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
}
