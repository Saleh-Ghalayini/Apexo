<?php

namespace App\Services;

use App\Models\Task;

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
    }
}
