<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SendTaskDeadlineReminders extends Command
{
    protected $signature = 'tasks:send-deadline-reminders';
    protected $description = 'Send AI-generated email reminders for tasks with deadlines in 1 day';

    public function handle()
    {
        $now = now();
        $in24h = $now->copy()->addDay();
        $tasks = \App\Models\Task::with('assignee')
            ->where('deadline', '>', $now)
            ->where('deadline', '<=', $in24h)
            ->where('status', '!=', 'completed')
            ->get();

        foreach ($tasks as $task)
            \App\Jobs\SendTaskDeadlineReminderJob::dispatch($task->id);

        $this->info('Dispatched jobs for ' . $tasks->count() . ' task reminders.');
    }
}
