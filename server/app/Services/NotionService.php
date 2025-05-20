<?php

namespace App\Services;

use App\Models\Integration;
use App\Models\IntegrationData;
use Illuminate\Support\Facades\Http;

class NotionService
{
    protected string $baseUrl = 'https://api.notion.com/v1';
    protected string $notionVersion = '2022-06-28';

    public function getDatabases(Integration $integration): array
    {
        try {
            $response = $this->makeRequest($integration, 'POST', '/search', [
                'filter' => [
                    'value' => 'database',
                    'property' => 'object'
                ]
            ]);

            if (!isset($response['results']) || !is_array($response['results']))
                return [];

            $databases = [];
            foreach ($response['results'] as $database)
                $databases[] = [
                    'id' => $database['id'],
                    'title' => $this->extractDatabaseTitle($database),
                    'description' => $this->extractDatabaseDescription($database),
                    'url' => $database['url'] ?? '',
                    'created_time' => $database['created_time'] ?? '',
                    'last_edited_time' => $database['last_edited_time'] ?? '',
                ];

            return $databases;
        } catch (\Exception $e) {
            return [];
        }
    }

    public function getDatabase(Integration $integration, string $databaseId): ?array
    {
        try {
            $response = $this->makeRequest($integration, 'GET', "/databases/{$databaseId}");

            if (!isset($response['id']))
                return null;

            return [
                'id' => $response['id'],
                'title' => $this->extractDatabaseTitle($response),
                'description' => $this->extractDatabaseDescription($response),
                'url' => $response['url'] ?? '',
                'properties' => $response['properties'] ?? [],
                'created_time' => $response['created_time'] ?? '',
                'last_edited_time' => $response['last_edited_time'] ?? '',
            ];
        } catch (\Exception $e) {
            return null;
        }
    }

    public function queryDatabase(Integration $integration, string $databaseId, array $query = []): array
    {
        try {
            $response = $this->makeRequest($integration, 'POST', "/databases/{$databaseId}/query", $query);

            if (!isset($response['results']) || !is_array($response['results']))
                return [];

            return $response['results'];
        } catch (\Exception $e) {
            return [];
        }
    }

    public function createPage(Integration $integration, string $databaseId, array $properties, array $children = []): ?array
    {
        try {
            $data = [
                'parent' => [
                    'database_id' => $databaseId
                ],
                'properties' => $properties
            ];

            if (!empty($children))
                $data['children'] = $children;

            $response = $this->makeRequest($integration, 'POST', '/pages', $data);

            if (!isset($response['id']))
                return null;

            return $response;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function updatePage(Integration $integration, string $pageId, array $properties): ?array
    {
        try {
            $response = $this->makeRequest($integration, 'PATCH', "/pages/{$pageId}", [
                'properties' => $properties
            ]);

            if (!isset($response['id']))
                return null;

            return $response;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function storeDatabase(Integration $integration, array $database): ?IntegrationData
    {
        try {
            return IntegrationData::updateOrCreate(
                [
                    'integration_id' => $integration->id,
                    'data_type' => 'notion_database',
                    'external_id' => $database['id'],
                ],
                [
                    'name' => $database['title'],
                    'description' => $database['description'] ?? null,
                    'data' => [
                        'url' => $database['url'] ?? null,
                        'properties' => $database['properties'] ?? [],
                        'created_time' => $database['created_time'] ?? null,
                        'last_edited_time' => $database['last_edited_time'] ?? null,
                    ],
                    'is_active' => true,
                ]
            );
        } catch (\Exception $e) {
            return null;
        }
    }

    protected function makeRequest(Integration $integration, string $method, string $endpoint, array $data = []): array
    {
        if (!$integration || $integration->provider !== 'notion')
            throw new \Exception('Invalid Notion integration');

        $url = $this->baseUrl . $endpoint;
        $headers = [
            'Authorization' => "Bearer {$integration->access_token}",
            'Notion-Version' => $this->notionVersion,
            'Content-Type' => 'application/json',
        ];

        try {
            $method = strtolower($method);
            $client = Http::withHeaders($headers);

            if ($method === 'get')
                $response = $client->get($url, $data);
            else
                $response = $client->$method($url, $data);

            if (!$response->successful()) {
                throw new \Exception("Notion API error: {$response->status()} - {$response->body()}");
            }

            return $response->json() ?? [];
        } catch (\Exception $e) {
            throw $e;
        }
    }

    protected function extractDatabaseTitle(array $database): string
    {
        if (isset($database['title']) && is_array($database['title'])) {
            $titles = array_map(function ($title) {
                return $title['plain_text'] ?? '';
            }, $database['title']);

            return implode('', $titles);
        }

        if (isset($database['properties']) && is_array($database['properties'])) {
            foreach ($database['properties'] as $property)
                if (isset($property['type']) && $property['type'] === 'title' && isset($property['title']) && is_array($property['title'])) {
                    $titles = array_map(fn($t) => $t['plain_text'] ?? '', $property['title']);
                    return implode('', $titles);
                }
        }

        return 'Untitled Database';
    }

    protected function extractDatabaseDescription(array $database): string
    {
        if (isset($database['description']) && is_array($database['description'])) {
            $descriptions = array_map(function ($desc) {
                return $desc['plain_text'] ?? '';
            }, $database['description']);

            return implode('', $descriptions);
        }

        return '';
    }

    public function formatPropertiesForPage(array $properties, array $schema): array
    {
        $formattedProperties = [];

        foreach ($properties as $key => $value) {
            if (!isset($schema[$key]))
                continue;

            $type = $schema[$key]['type'] ?? null;
            $formattedProperties[$key] = $this->formatPropertyByType($type, $value);
        }

        return $formattedProperties;
    }

    protected function formatPropertyByType(?string $type, $value): array
    {
        switch ($type) {
            case 'title':
                return [
                    'title' => [
                        ['text' => ['content' => (string) $value]]
                    ]
                ];
            case 'rich_text':
                return [
                    'rich_text' => [
                        ['text' => ['content' => (string) $value]]
                    ]
                ];
            case 'number':
                return ['number' => is_numeric($value) ? $value : null];
            case 'select':
                return ['select' => ['name' => (string) $value]];
            case 'multi_select':
                $multiSelectValues = is_array($value) ? $value : explode(',', (string)$value);
                return ['multi_select' => array_map(fn($v) => ['name' => trim($v)], $multiSelectValues)];
            case 'date':
                $dateStr = is_string($value) ? $value : null;
                return ['date' => ['start' => $dateStr]];
            case 'people':
                $people = is_array($value) ? $value : [];
                return ['people' => array_map(fn($id) => ['id' => $id], $people)];
            case 'checkbox':
                return ['checkbox' => (bool)$value];
            case 'url':
                return ['url' => (string) $value];
            case 'email':
                return ['email' => (string) $value];
            case 'phone_number':
                return ['phone_number' => (string) $value];
            case 'formula':
            case 'relation':
            case 'rollup':
                return [];
            default:
                return [];
        }
    }

    public function handleOAuthCallback($request): array
    {
        $state = $request->session()->pull('notion_oauth_state');
        if (!$state || $state !== $request->state)
            return ['success' => false, 'message' => 'Invalid state parameter'];

        if (!$request->has('code'))
            return ['success' => false, 'message' => 'Authorization code missing'];

        $code = $request->code;
        $clientId = config('services.notion.client_id');
        $clientSecret = config('services.notion.client_secret');
        $redirectUri = config('services.notion.redirect');
        $response = Http::asForm()->post('https://api.notion.com/v1/oauth/token', [
            'grant_type' => 'authorization_code',
            'code' => $code,
            'redirect_uri' => $redirectUri,
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
        ]);

        if (!$response->successful())
            return ['success' => false, 'message' => 'Failed to get access token: ' . $response->body()];

        $data = $response->json();
        $accessToken = $data['access_token'];
        $workspaceId = $data['workspace_id'];
        $workspaceName = $data['workspace_name'] ?? 'Notion Workspace';
        $botId = $data['bot_id'];
        $expiresAt = now()->addSeconds($data['expires_in']);
        $userResponse = Http::withToken($accessToken)
            ->withHeaders(['Notion-Version' => '2022-06-28'])
            ->get('https://api.notion.com/v1/users');
        $userEmail = null;
        if ($userResponse->successful()) {
            $users = $userResponse->json('results', []);
            foreach ($users as $user)
                if (isset($user['type']) && $user['type'] === 'person') {
                    $userEmail = $user['person']['email'] ?? null;
                    break;
                }
        }

        $userId = $request->user()?->id ?? $request->session()->pull('notion_oauth_user_id');
        if (!$userId)
            return ['success' => false, 'message' => 'User ID not found. Please try authenticating again.'];

        \App\Models\Integration::updateOrCreate(
            [
                'user_id' => $userId,
                'provider' => 'notion',
            ],
            [
                'token_type' => 'Bearer',
                'access_token' => $accessToken,
                'refresh_token' => null,
                'expires_at' => $expiresAt,
                'status' => 'active',
                'metadata' => [
                    'workspace_id' => $workspaceId,
                    'workspace_name' => $workspaceName,
                    'bot_id' => $botId,
                    'user_email' => $userEmail,
                ],
            ]
        );

        return ['success' => true];
    }
}
