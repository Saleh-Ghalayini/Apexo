<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\AIService;
use Illuminate\Bus\Queueable;
use App\Models\EmployeeAnalytics;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldQueue;

class ProcessEmployeeAnalyticsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $userId;
    public $periodStart;
    public $periodEnd;

    public function __construct($userId, $periodStart, $periodEnd)
    {
        $this->userId = $userId;
        $this->periodStart = $periodStart;
        $this->periodEnd = $periodEnd;
    }

    public function handle(AIService $aiService)
    {
        $user = User::find($this->userId);
        if (!$user) return;

        $meetings = $user->meetings()->whereBetween('scheduled_at', [$this->periodStart, $this->periodEnd])->get();
        $tasks = $user->tasks()->whereBetween('created_at', [$this->periodStart, $this->periodEnd])->get();

        $analytics = $aiService->analyzeEmployeePerformance($user, $meetings, $tasks, $this->periodStart, $this->periodEnd);
        EmployeeAnalytics::updateOrCreate(
            [
                'user_id' => $this->userId,
                'period_start' => $this->periodStart,
                'period_end' => $this->periodEnd,
            ],
            [
                'analytics' => $analytics,
            ]
        );
    }
}
