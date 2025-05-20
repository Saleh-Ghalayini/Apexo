<?php

namespace App\Services;

use App\Services\Tools\ReportToolsService;
use App\Services\Tools\MeetingToolsService;
use App\Services\Tools\CalendarToolsService;
use App\Services\Tools\EmployeeToolsService;

class AIToolsService
{
    protected $dataAccessService;
    protected $meetingToolsService;
    protected $reportToolsService;
    protected $calendarToolsService;
    protected $employeeToolsService;

    public function __construct($dataAccessService)
    {
        $this->dataAccessService = $dataAccessService;
        $this->meetingToolsService = new MeetingToolsService($dataAccessService);
        $this->reportToolsService = new ReportToolsService($dataAccessService);
        $this->calendarToolsService = new CalendarToolsService($dataAccessService);
        $this->employeeToolsService = new EmployeeToolsService($dataAccessService);
    }
}
