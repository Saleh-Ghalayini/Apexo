<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use App\Services\SlackOAuthService;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\SlackCallbackRequest;

class SlackOAuthController extends Controller
{
    use ResponseTrait;
    protected SlackOAuthService $slackOAuthService;

    public function __construct(SlackOAuthService $slackOAuthService)
    {
        $this->slackOAuthService = $slackOAuthService;
    }

    public function redirectToSlack(Request $request)
    {
        $userId = $request->query('user_id');
        if (!$userId) {
            $user = Auth::user();
            if (!$user) return $this->errorResponse('Unauthorized', 401);
            $userId = $user->id;
        }
        $url = $this->slackOAuthService->getSlackRedirectUrl($userId);
        return redirect($url);
    }

    public function handleSlackCallback(SlackCallbackRequest $request)
    {
        $validated = $request->validated();
        $result = $this->slackOAuthService->handleCallback($validated['code'], $validated['state']);
        if (isset($result['error'])) return $this->errorResponse($result['error'], $result['status'] ?? 400);

        return redirect($result['redirect']);
    }
}
