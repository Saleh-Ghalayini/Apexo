<?php

namespace App\Services;

use App\Models\NotionPage;
use Illuminate\Support\Str;
use App\Traits\ExecuteExternalServiceTrait;

class NotionService
{
    use ExecuteExternalServiceTrait;

    private const DEFAULT_TITLE = 'Untitled Task';

    public function create($user, $data)
    {
        $notion_key = config('notion.api_key');
        $notion_db_Id = config('notion.database_id');

        $headers = [
            'Authorization' => "Bearer {$notion_key}",
            'Notion-Version' => config('notion.version'),
            'Content-Type' => 'application/json',
        ];

        $payload = [
            'parent' => [
                'database_id' => $notion_db_Id,
            ],
            'properties' => $this->formatProperties($data, $user),
        ];

        $response = $this->request('POST', config('notion.api_url'), $headers, $payload);

        if (!$response->successful()) {
            $errorDetails = $response->json();
            $errorMessage = $errorDetails['message'] ?? 'An unknown error occurred.';
            $statusCode = $response->status();

            return [
                'status' => 'error',
                'data' => [],
                'message' => "Failed to create task in Notion. Error: {$errorMessage}",
                'status_code' => $statusCode,
                'details' => $errorDetails,
            ];
        }

        $notionPage = new NotionPage();
        $notionPage->user_id = $user->id;
        $notionPage->page_id = $response->json('id');
        $notionPage->title = $data['title'] ?? self::DEFAULT_TITLE;
        $notionPage->save();

        return [
            'status' => 'success',
            'data' => [
                'page_id' => $response->json('id'),
                'notion_response' => $response->json(),
            ],
            'message' => 'Task created in Notion successfully.',
        ];
    }
}
