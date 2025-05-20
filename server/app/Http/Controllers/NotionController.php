<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Integration;
use App\Models\IntegrationData;
use App\Services\NotionService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\NotionDatabaseRequest;

class NotionController extends Controller
{
    protected NotionService $notionService;

    public function __construct(NotionService $notionService)
    {
        $this->notionService = $notionService;
    }

    public function getDatabases(Request $request)
    {
        try {
            $integration = $this->getNotionIntegration();
            if (!$integration)
                return response()->json([
                    'success' => false,
                    'message' => 'Notion integration not found or inactive'
                ], 404);

            $databases = $this->notionService->getDatabases($integration);
            return response()->json([
                'success' => true,
                'payload' => $databases
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting Notion databases', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to get Notion databases: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getDatabase(NotionDatabaseRequest $request, $databaseId)
    {
        try {
            $integration = $this->getNotionIntegration();
            if (!$integration)
                return response()->json([
                    'success' => false,
                    'message' => 'Notion integration not found or inactive'
                ], 404);

            $database = $this->notionService->getDatabase($integration, $databaseId);
            if (!$database)
                return response()->json([
                    'success' => false,
                    'message' => 'Database not found'
                ], 404);

            return response()->json([
                'success' => true,
                'payload' => $database
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting Notion database', [
                'database_id' => $databaseId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to get Notion database: ' . $e->getMessage()
            ], 500);
        }
    }

    public function queryDatabase(Request $request, $databaseId)
    {
        try {
            $integration = $this->getNotionIntegration();
            if (!$integration)
                return response()->json([
                    'success' => false,
                    'message' => 'Notion integration not found or inactive'
                ], 404);

            $query = $request->json()->all();
            $pages = $this->notionService->queryDatabase($integration, $databaseId, $query);
            return response()->json([
                'success' => true,
                'payload' => $pages
            ]);
        } catch (\Exception $e) {
            Log::error('Error querying Notion database', [
                'database_id' => $databaseId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to query Notion database: ' . $e->getMessage()
            ], 500);
        }
    }

    public function createPage(Request $request, $databaseId)
    {
        try {
            $integration = $this->getNotionIntegration();
            if (!$integration)
                return response()->json([
                    'success' => false,
                    'message' => 'Notion integration not found or inactive'
                ], 404);

            $properties = $request->json('properties', []);
            $children = $request->json('children', []);
            $page = $this->notionService->createPage($integration, $databaseId, $properties, $children);
            if (!$page)
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create page'
                ], 500);

            return response()->json([
                'success' => true,
                'payload' => $page
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error creating Notion page', [
                'database_id' => $databaseId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to create Notion page: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updatePage(Request $request, $pageId)
    {
        try {
            $integration = $this->getNotionIntegration();
            if (!$integration)
                return response()->json([
                    'success' => false,
                    'message' => 'Notion integration not found or inactive'
                ], 404);

            $properties = $request->json('properties', []);
            $page = $this->notionService->updatePage($integration, $pageId, $properties);
            if (!$page)
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update page'
                ], 500);

            return response()->json([
                'success' => true,
                'payload' => $page
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating Notion page', [
                'page_id' => $pageId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to update Notion page: ' . $e->getMessage()
            ], 500);
        }
    }

    public function saveDatabase(Request $request, $databaseId)
    {
        try {
            $integration = $this->getNotionIntegration();
            if (!$integration)
                return response()->json([
                    'success' => false,
                    'message' => 'Notion integration not found or inactive'
                ], 404);

            $database = $this->notionService->getDatabase($integration, $databaseId);
            if (!$database)
                return response()->json([
                    'success' => false,
                    'message' => 'Database not found'
                ], 404);

            $savedDatabase = $this->notionService->storeDatabase($integration, $database);
            if (!$savedDatabase)
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to save database'
                ], 500);

            return response()->json([
                'success' => true,
                'payload' => $savedDatabase
            ]);
        } catch (\Exception $e) {
            Log::error('Error saving Notion database', [
                'database_id' => $databaseId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to save Notion database: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getSavedDatabases(Request $request)
    {
        try {
            $integration = $this->getNotionIntegration();
            if (!$integration)
                return response()->json([
                    'success' => false,
                    'message' => 'Notion integration not found or inactive'
                ], 404);

            $databases = IntegrationData::where('integration_id', $integration->id)
                ->where('data_type', 'notion_database')
                ->where('is_active', true)
                ->get();

            return response()->json([
                'success' => true,
                'payload' => $databases
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting saved Notion databases', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to get saved Notion databases: ' . $e->getMessage()
            ], 500);
        }
    }

    protected function getNotionIntegration(): ?Integration
    {
        $user = Auth::user();
        $query = Integration::where('user_id', $user->id)
            ->where('provider', 'notion');
        $tableColumns = \Illuminate\Support\Facades\Schema::getColumnListing('integrations');
        if (in_array('status', $tableColumns))
            $query->where('status', 'active');
        return $query->first();
    }
}
