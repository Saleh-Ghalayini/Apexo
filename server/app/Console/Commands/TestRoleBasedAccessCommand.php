<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use App\Services\DataAccessService;
use Illuminate\Support\Facades\Auth;

class TestRoleBasedAccessCommand extends Command
{
    protected $signature = 'test:role-access {email} {tool} {--param=}';
    protected $description = 'Test role-based data access for AI tools';

    public function handle(DataAccessService $dataAccessService)
    {
        $email = $this->argument('email');
        $tool = $this->argument('tool');
        $paramString = $this->option('param');

        $params = [];
        if ($paramString) {
            list($key, $value) = explode(':', $paramString);
            $params[$key] = $value;
        }
        \Illuminate\Support\Facades\DB::enableQueryLog();

        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("User with email {$email} not found.");
            return 1;
        }

        $this->info("Testing access as user: {$user->name} ({$user->role})");
        $this->info("Tool: {$tool}");
        $this->info("Parameters: " . json_encode($params));
        $this->newLine();

        Auth::login($user);

        try {
            $result = [];

            switch ($tool) {
                case 'get_user_tasks':
                    $result = $dataAccessService->getUserTasks($user, $params);
                    break;
                case 'get_user_meetings':
                    $result = $dataAccessService->getUserMeetings($user, $params);
                    break;
                case 'get_employee_info':
                    $employeeId = $params['employee_identifier'] ?? ($params['id'] ?? '');
                    $result = $dataAccessService->getEmployeeInfo($user, $employeeId);
                    break;
                case 'get_department_analytics':
                    $result = $dataAccessService->getDepartmentAnalytics($user, $params);
                    break;
                default:
                    $this->error("Unknown tool: {$tool}");
                    return 1;
            }

            $this->info("Result:");
            $this->newLine();
            $this->line(json_encode($result, JSON_PRETTY_PRINT));

            return 0;
        } catch (\Exception $e) {
            $this->error("Error: {$e->getMessage()}");
            $this->error("SQL: " . \Illuminate\Support\Facades\DB::getQueryLog()[0]['query'] ?? 'No query logged');
            $this->line($e->getTraceAsString());
            return 1;
        }
    }
}
