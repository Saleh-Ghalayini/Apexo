<?php

namespace App\Services\Tools;

use Prism\Prism\Facades\Tool;
use Prism\Prism\Schema\StringSchema;
use Prism\Prism\Schema\ObjectSchema;

class CalendarToolsService
{
    protected \App\Services\DataAccessService $dataAccessService;

    public function __construct(\App\Services\DataAccessService $dataAccessService)
    {
        $this->dataAccessService = $dataAccessService;
    }

    public function getToolsForUser($user): array
    {
        $tools = [];
        $tools[] = Tool::as('create_google_calendar_event')
            ->for('Create a Google Calendar event for the user. Do not use this tool for sending emails. Returns the created event details.')
            ->withParameter(
                new ObjectSchema(
                    'event',
                    'Event details',
                    [
                        new StringSchema('summary', 'Event title'),
                        new StringSchema('start', 'Start datetime (ISO8601)'),
                        new StringSchema('end', 'End datetime (ISO8601)'),
                        new StringSchema('description', 'Event description (optional)'),
                        new StringSchema('attendees', 'Comma-separated emails of attendees (optional)')
                    ]
                )
            )
            ->using(function (array $event) {
                return json_encode([
                    'event' => $event
                ]);
            });
        return $tools;
    }
}
