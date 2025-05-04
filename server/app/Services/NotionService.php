<?php

namespace App\Services;

use App\Traits\ExecuteExternalServiceTrait;

class NotionService
{
    use ExecuteExternalServiceTrait;

    public function create($user, $data)
    {
        $notionToken = env('NOTION_API_KEY');
        $notionDatabaseId = env('NOTION_DB_ID');

        $headers = [
            'Authorization' => "Bearer {$notionToken}",
            'Notion-Version' => env('NOTION_API_VERSION', '2022-06-28'),
            'Content-Type' => 'application/json',
        ];

        $payload = [
            'parent' => [
                'database_id' => $notionDatabaseId,
            ],
            'properties' => [
                'Title' => [
                    'title' => [
                        [
                            'text' => [
                                'content' => $data['title'] ?? 'Untitled Task',
                            ]
                        ]
                    ]
                ],
                'Due Date' => [
                    'date' => [
                        'start' => $data['due_date'] ?? now()->toDateString(),
                    ]
                ],
                'Assignee' => [
                    'rich_text' => [
                        [
                            'text' => [
                                'content' => $data['assignee'] ?? $user->name,
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $response = $this->request('POST', env('NOTION_API_URL'), $headers, $payload);

        if (!$response->successful()) {
            return [
                'status' => 'error',
                'data' => [],
                'message' => 'Failed to create task in Notion',
                'details' => $response->json()
            ];
        }

        return [
            'status' => 'success',
            'data' => $response->json(),
            'message' => 'Task created in Notion successfully.',
        ];
    }

    public function update($user, $data) {}

    public function assign($user, $data) {}

    public function track($user, $data) {}

    public function delete($user, $data) {}
}
