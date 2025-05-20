<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\NotionService;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\NotionCallbackRequest;

class NotionOAuthController extends Controller
{
    protected NotionService $notionService;

    public function __construct(NotionService $notionService)
    {
        $this->notionService = $notionService;
    }

    public function redirectToNotion(Request $request)
    {
        $clientId = config('services.notion.client_id');
        $redirectUri = config('services.notion.redirect');

        if (empty($clientId) || empty($redirectUri)) {
            return response()->json([
                'success' => false,
                'message' => 'Notion OAuth configuration missing'
            ], 500);
        }

        $userId = $request->query('user_id');
        if ($userId) $request->session()->put('notion_oauth_user_id', $userId);

        $state = bin2hex(random_bytes(16));
        $request->session()->put('notion_oauth_state', $state);

        $queryString = http_build_query([
            'client_id' => $clientId,
            'response_type' => 'code',
            'redirect_uri' => $redirectUri,
            'state' => $state,
        ]);

        return redirect('https://api.notion.com/v1/oauth/authorize?' . $queryString);
    }

    public function handleNotionCallback(NotionCallbackRequest $request)
    {
        try {
            $result = $this->notionService->handleOAuthCallback($request);
            if ($result['success']) return redirect('/integrations?status=success&provider=notion');

            return redirect('/integrations?status=error&provider=notion&message=' . urlencode($result['message']));
        } catch (\Exception $e) {
            Log::error('Notion OAuth error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect('/integrations?status=error&provider=notion&message=' . urlencode($e->getMessage()));
        }
    }
}
