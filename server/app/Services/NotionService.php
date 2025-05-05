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

    public function update($user, $data)
    {
        $notionToken = config('notion.api_key');

        $prompt = $data['prompt'] ?? $data['payload']['prompt'] ?? null;

        if (!$prompt) {
            return [
                'status' => 'error',
                'message' => 'Prompt is required for updating a task.',
                'data' => [],
            ];
        }

        $parsed = $this->parsePrompt($prompt);

        if (empty($parsed['page_title']) || empty($parsed['properties'])) {
            return [
                'status' => 'error',
                'message' => 'Failed to extract page title or properties from prompt.',
                'data' => [],
            ];
        }

        $page = NotionPage::where('title', $parsed['page_title'])->where('user_id', $user->id)->first();

        if (!$page) {
            return [
                'status' => 'error',
                'message' => 'Page not found for user.',
                'data' => [],
            ];
        }

        $data['page_id'] = $page->page_id;
        $data = array_merge($data, $parsed['properties']);

        $headers = [
            'Authorization' => "Bearer {$notionToken}",
            'Notion-Version' => config('notion.version'),
            'Content-Type' => 'application/json',
        ];

        $payload = [
            'properties' => $this->formatProperties($data, $user),
        ];

        $url = "https://api.notion.com/v1/pages/{$data['page_id']}";

        $response = $this->request('PATCH', $url, $headers, $payload);

        if (!$response->successful()) {
            return [
                'status' => 'error',
                'message' => 'Failed to update task in Notion',
                'data' => [],
                'details' => $response->json(),
            ];
        }

        return [
            'status' => 'success',
            'message' => 'Task updated in Notion successfully.',
            'data' => $response->json(),
        ];
    }

    public function assign($user, $data)
    {
        return [
            'status' => 'not_implemented',
            'message' => 'Assigning Notion tasks is not implemented yet.',
            'data' => [],
        ];
    }

    public function track($user, $data)
    {
        return [
            'status' => 'not_implemented',
            'message' => 'Tracking Notion tasks is not implemented yet.',
            'data' => [],
        ];
    }

    public function delete($user, $data)
    {
        $page = NotionPage::where('title', $data['title'])->where('user_id', $user->id)->first();

        if (!$page) {
            return [
                'status' => 'error',
                'message' => 'Page not found for deletion.',
                'data' => [],
            ];
        }

        $url = "https://api.notion.com/v1/pages/{$page->page_id}";

        $headers = [
            'Authorization' => "Bearer " . config('notion.api_key'),
            'Notion-Version' => config('notion.version'),
            'Content-Type' => 'application/json',
        ];

        $response = $this->request('PATCH', $url, $headers, ['archived' => true]);

        if (!$response->successful()) {
            return [
                'status' => 'error',
                'message' => 'Failed to archive (delete) the task in Notion.',
                'data' => [],
            ];
        }

        $page->delete();

        return [
            'status' => 'success',
            'message' => 'Task archived (deleted) from Notion successfully.',
            'data' => [],
        ];
    }

    private function formatProperties(array $data, $user): array
    {
        $properties = [];

        foreach ($data as $property => $value) {
            if ($property === 'page_id' || is_array($value) || empty($value))
                continue;

            $formatted_prop = ucwords(str_replace('_', ' ', $property));

            if (str_contains(strtolower($formatted_prop), 'date') || strtolower($formatted_prop) === 'due date') {
                $properties[$formatted_prop] = ['date' => ['start' => $value]];
            } elseif (str_contains(strtolower($formatted_prop), 'title')) {
                $properties[$formatted_prop] = ['title' => [[
                    'text' => ['content' => $value]
                ]]];
            } else {
                $properties[$formatted_prop] = ['rich_text' => [[
                    'text' => ['content' => $value]
                ]]];
            }
        }

        if (!isset($properties['Title'])) {
            $properties['Title'] = ['title' => [[
                'text' => ['content' => self::DEFAULT_TITLE]
            ]]];
        }

        return $properties;
    }

    private function parsePrompt(string $prompt): array
    {
        $properties = [];

        preg_match_all('/([\w\s]+):\s*(.+?)(?=\s+\w+:|$)/', $prompt, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $key = ucwords(trim($match[1]));
            $value = trim($match[2]);
            $properties[$key] = $value;
        }

        return [
            'page_title' => $this->extractTitle($prompt),
            'properties' => $properties,
        ];
    }

    private function extractTitle(string $prompt): string
    {
        preg_match('/"([^"]+)"/', $prompt, $matches);
        return $matches[1] ?? self::DEFAULT_TITLE;
    }
}
