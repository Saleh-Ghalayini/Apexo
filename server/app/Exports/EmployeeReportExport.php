<?php

namespace App\Exports;

use App\Models\User;
use App\Models\EmployeeAnalytics;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;

class EmployeeReportExport implements FromArray, WithTitle
{
    protected $user;
    protected $analytics;
    protected $employeeAnalytics;

    public function __construct(User $user, $analytics, EmployeeAnalytics $employeeAnalytics)
    {
        $this->user = $user;
        $this->analytics = $analytics;
        $this->employeeAnalytics = $employeeAnalytics;
    }

    public function array(): array
    {
        return [
            ['Employee Report'],
            ['Name', $this->user->name],
            ['Period', $this->employeeAnalytics->period_start . ' to ' . $this->employeeAnalytics->period_end],
            ['Summary', $this->analytics['summary'] ?? ''],
            ['Meetings Attended', $this->analytics['meetings_attended'] ?? ''],
            ['Tasks Completed', $this->analytics['tasks_completed'] ?? ''],
            ['Tasks Assigned', $this->analytics['tasks_assigned'] ?? ''],
            ['Sentiment', $this->analytics['sentiment'] ?? ''],
            ['Notable Achievements', isset($this->analytics['notable_achievements']) ? implode(', ', $this->analytics['notable_achievements']) : ''],
            ['Areas for Improvement', isset($this->analytics['areas_for_improvement']) ? implode(', ', $this->analytics['areas_for_improvement']) : ''],
        ];
    }

    public function title(): string
    {
        return 'Employee Report';
    }
}
