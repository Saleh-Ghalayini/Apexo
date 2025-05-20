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
    }
}
