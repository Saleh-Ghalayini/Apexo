<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Company;
use Illuminate\Console\Command;
use App\Services\DataAccessService;
use Illuminate\Support\Facades\Auth;

class TestHRAccessCommand extends Command
{
    protected $signature = 'test:hr-access {--seed-test-data}';
    protected $description = 'Test HR user access restrictions across companies';

    public function handle(DataAccessService $dataAccessService)
    {
        if ($this->option('seed-test-data')) {
            $this->call('db:seed', ['--class' => 'Database\\Seeders\\TestMultiCompanySeeder']);
        }
        $hrUsers = User::where('role', 'hr')->get();
        $companies = Company::all();

        $this->info('Found ' . $hrUsers->count() . ' HR users across ' . $companies->count() . ' companies');
        $this->newLine();

        foreach ($hrUsers as $hrUser) {
            $this->info("Testing as HR User: {$hrUser->name} (Company: {$hrUser->company->name})");

            Auth::login($hrUser);

            $this->info("Test 1: Getting employees from {$hrUser->company->name}");
            $result = $dataAccessService->getEmployeeInfo($hrUser, "Employee");
            $this->displayResult($result);

            $this->info("Test 2: Searching for employees in other companies");
            $otherCompanyEmployee = User::where('company_id', '!=', $hrUser->company_id)
                ->where('role', 'employee')
                ->first();

            if ($otherCompanyEmployee) {
                $result = $dataAccessService->getEmployeeInfo($hrUser, $otherCompanyEmployee->name);
                $this->displayResult($result);
            } else  $this->warn("No employee found in other companies for testing");

            $this->info("Test 3: Getting specific employee by ID from other company");
            $otherCompanyEmployee = User::where('company_id', '!=', $hrUser->company_id)->first();

            if ($otherCompanyEmployee) {
                $result = $dataAccessService->getEmployeeInfo($hrUser, $otherCompanyEmployee->id);
                $this->displayResult($result);
            } else  $this->warn("No employee found in other companies for testing");

            $this->newLine();
        }

        return 0;
    }

    private function displayResult($result)
    {
        if ($result['success']) {
            $this->info("Success: Found employee data");
            $data = $result['data'];

            if (is_array($data)) {
                foreach ($data as $key => $value) {
                    if ($key === 'company_id') {
                        $company = Company::find($value);
                        $companyName = $company ? $company->name : 'Unknown';
                        $this->line("  {$key}: {$value} ({$companyName})");
                    } else  $this->line("  {$key}: " . (is_array($value) ? json_encode($value) : $value));
                }
            }
        } else  $this->error("Failed: " . ($result['error'] ?? 'Unknown error'));
    }
}
