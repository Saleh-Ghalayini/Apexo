<?php

namespace App\Services\Tools;

use Prism\Prism\Facades\Tool;
use Prism\Prism\Schema\EnumSchema;
use Prism\Prism\Schema\StringSchema;
use Prism\Prism\Schema\ObjectSchema;

class ReportToolsService
{
    protected \App\Services\DataAccessService $dataAccessService;

    public function __construct(\App\Services\DataAccessService $dataAccessService)
    {
        $this->dataAccessService = $dataAccessService;
    }

    public function getToolsForUser($user): array
    {
        $tools = [];
        if ($user->isHR() || $user->isManager()) {
            $tools[] = Tool::as('generate_report')
                ->for('Generate a report (task summary, meeting summary, productivity, etc.) for a given date range and type.')
                ->withParameter(
                    new ObjectSchema(
                        'params',
                        'Report generation parameters',
                        [
                            new EnumSchema('type', 'Type of report', ['task_summary', 'meeting_summary', 'productivity']),
                            new StringSchema('period_start', 'Start date (YYYY-MM-DD)'),
                            new StringSchema('period_end', 'End date (YYYY-MM-DD)'),
                            new StringSchema('filters', 'Additional filters as JSON (optional)')
                        ]
                    )
                )
                ->using(function (array $params = []) use ($user) {
                    return json_encode($this->dataAccessService->generateReport($user, $params));
                });
        }
        return $tools;
    }
}
