<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        \App\Console\Commands\TestAIChatCommand::class,
        \App\Console\Commands\TestRoleBasedAccessCommand::class,
        \App\Console\Commands\SendTaskDeadlineReminders::class,
    ];

    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('tasks:send-deadline-reminders')->everyMinute();
    }

    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
