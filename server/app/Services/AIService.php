<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Traits\ExecuteExternalServiceTrait;

class AIService
{
    use ExecuteExternalServiceTrait;

    public function handlePrompt($user, $prompt)
    {
        $content = 'You are Apexo, a smart AI assistant designed to help businesses automate workflows and improve productivity.

                    Your role includes:
                    - Participating in meetings (via transcribed speech) and summarizing key points.
                    - Extracting and assigning tasks automatically when someone is instructed to do something.
                    - Helping employees track, manage, and complete their tasks on time.
                    - Answering questions related to company policies, tasks, or project progress.
                    - Integrating with tools like Slack, Email, Notion, Trello, and Google Calendar to send updates, reminders, or follow-ups.
                    - Automating repetitive admin work like writing reports or setting up meetings.

                    Always be accurate, concise, and helpful.
                    When a user gives a prompt, analyze their intent and suggest or perform the necessary action (e.g., task creation, sending reminders, fetching updates).
                    If an action is needed, return a JSON structure describing what needs to be done (e.g., "create_task", "send_reminder", etc.) along with details.';

        $headers = [
            'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
            'Content-Type' => 'application/json',
        ];

        $data = [
            'model' => 'gpt-3.5-turbo-1106',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => $content,
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

        return $response->json('choices.0.message.content');
    }
}
