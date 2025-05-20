<?php

namespace App\Services\Tools;

use Prism\Prism\Facades\Tool;
use Prism\Prism\Schema\StringSchema;

class EmployeeToolsService
{
    protected \App\Services\DataAccessService $dataAccessService;

    public function __construct(\App\Services\DataAccessService $dataAccessService)
    {
        $this->dataAccessService = $dataAccessService;
    }

    public function getToolsForUser($user): array
    {
        $tools = [];
        $tools[] = Tool::as('get_employee_info')
            ->for('Get employee information based on user permissions')
            ->withParameter(
                new StringSchema('employee_identifier', 'Employee ID or name to look up')
            )
            ->using(function (string $employee_identifier) use ($user) {
                return json_encode($this->dataAccessService->getEmployeeInfo($user, $employee_identifier));
            });
        $tools[] = Tool::as('send_email')
            ->for('Send an email to any address. Use this tool only for sending emails, not for calendar invites or events.')
            ->withParameter(new StringSchema('to', 'Recipient email address'))
            ->withParameter(new StringSchema('subject', 'Subject of the email'))
            ->withParameter(new StringSchema('body', 'Body/content of the email'))
            ->using(function (string $to, string $subject, string $body) {
                return json_encode([
                    'to' => $to,
                    'subject' => $subject,
                    'body' => $body
                ]);
            });
        return $tools;
    }
}
