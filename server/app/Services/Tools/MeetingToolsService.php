<?php

namespace App\Services\Tools;

use Prism\Prism\Facades\Tool;
use Prism\Prism\Schema\StringSchema;
use Prism\Prism\Schema\ObjectSchema;

class MeetingToolsService
{
    protected \App\Services\DataAccessService $dataAccessService;

    public function __construct(\App\Services\DataAccessService $dataAccessService)
    {
        $this->dataAccessService = $dataAccessService;
    }

    public function getToolsForUser($user): array
    {
        $tools = [];
        $tools[] = Tool::as('get_user_meetings')
            ->for('Get meetings for the user based on their permissions')
            ->withParameter(
                new ObjectSchema(
                    'params',
                    'Query parameters',
                    [
                        new StringSchema('date', 'Filter meetings by date (YYYY-MM-DD or "today", "week")')
                    ]
                )
            )
            ->using(function (array $params = []) use ($user) {
                return json_encode($this->dataAccessService->getUserMeetings($user, $params));
            });
        return $tools;
    }
}
