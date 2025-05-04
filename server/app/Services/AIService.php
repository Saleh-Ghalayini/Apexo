<?php

namespace App\Services;

use App\Services\TaskService;
use App\Traits\ExecuteExternalServiceTrait;

class AIService
{
    use ExecuteExternalServiceTrait;

    private function dispatchIntent($user, array $structured): array
    {
        $intent = $structured['intent'] ?? 'none';
        $action = $structured['action'] ?? null;
        $data = $structured['data'] ?? [];

        switch ($intent) {
            case 'task_management':
                return app(TaskService::class)->handleAIAction($user, $action, $data);
            default:
                return [
                    'intent' => 'none',
                    'data' => [],
                    'message' => 'I could not understand your request.',
                ];
        }
    }

    private function processAIResponse($user, string $response_text): array
    {
        try {
            $structured = json_decode($response_text, true, 512, JSON_THROW_ON_ERROR);
        } catch (\Throwable $e) {
            return [
                'intent' => 'none',
                'data' => [],
                'message' => 'Could not understand your request',
            ];
        }

        return $this->dispatchIntent($user, $structured);
    }

    public function handlePrompt($user, $prompt)
    {
        $headers = [
            'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
            'Content-Type' => 'application/json',
        ];

        $data = [
            'model' => 'gpt-3.5-turbo-1106',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => $this->systemPrompt(),
                ],
                [
                    'role' => 'user',
                    'content' => $prompt,
                ]
            ]
        ];

        $response = $this->request('POST', env('OPENAI_API_URL'), $headers, $data);

        if (!$response->successful())
            return [
                'error' => true,
                'message' => 'Sorry, something went wrong with the AI request.',
                'details' => $response->json(),
            ];

        return $this->processAIResponse($user, $response->json('choices.0.message.content'));
    }

    private function systemPrompt(): string
    {
        return <<<EOT
                You are Apexo, an AI assistant for business productivity and task automation.

                ONLY respond to prompts that relate to:
                - Creating, updating, or managing tasks.
                - Sending reminders or announcements to team communication tools (Slack, Email, etc.).
                - Generating or summarizing reports.
                - Managing meetings or calendars.
                - Providing updates or information about project progress or team workflows.

                If a user sends a prompt that is not related to work productivity or business automation, politely tell them:
                "I'm here to help you with work-related tasks and automation. Please try something like creating a task, setting a reminder, or summarizing a meeting."

                When responding, always provide a JSON structure like:
                {
                "intent": "task_management",
                "action": "create", // or "update", "assign", "track"
                "data": {
                    "title": "Prepare Q2 Report",
                    "due_date": "2025-06-01",
                    "assignee": "John Doe"
                },
                "message": "I will create a task titled 'Prepare Q2 Report' assigned to John Doe due on June 1, 2025."
                }

                If the prompt is not actionable, return:
                {
                "intent": "none",
                "action": null,
                "data": {},
                "message": "I'm here to help you with work-related tasks and automation. Please try something like creating a task, setting a reminder, or summarizing a meeting."
                }
                EOT;
    }
}
