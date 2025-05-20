<?php

namespace App\Services;

use App\Models\User;
use App\Models\Integration;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Crypt;
use App\Models\IntegrationCredential;

class SlackOAuthService
{
    public function getSlackRedirectUrl($userId)
    {
        $clientId = config('services.slack.client_id');
        $redirectUri = config('services.slack.redirect');
        $scopes = 'chat:write,channels:read,groups:read,users:read';
        $state = Crypt::encrypt($userId);
        $url = "https://slack.com/oauth/v2/authorize?client_id={$clientId}&scope={$scopes}&redirect_uri={$redirectUri}&state={$state}";
        return $url;
    }

    public function handleCallback($code, $state)
    {
        try {
            $userId = Crypt::decrypt($state);
            $user = User::findOrFail($userId);
        } catch (\Exception $e) {
            return ['error' => 'Invalid or expired state', 'status' => 400];
        }

        $redirectUri = config('services.slack.redirect');
        $response = Http::asForm()->post('https://slack.com/api/oauth.v2.access', [
            'client_id' => config('services.slack.client_id'),
            'client_secret' => config('services.slack.client_secret'),
            'code' => $code,
            'redirect_uri' => $redirectUri,
        ]);
        $data = $response->json();
        if (!$data['ok'])   return ['error' => 'Slack OAuth failed', 'details' => $data, 'status' => 400];

        $integration = Integration::firstOrCreate([
            'user_id' => $user->id,
            'provider' => 'slack',
        ]);

        IntegrationCredential::updateOrCreate(
            [
                'integration_id' => $integration->id,
                'user_id' => $user->id,
                'type' => 'slack',
            ],
            [
                'access_token' => $data['access_token'] ?? $data['authed_user']['access_token'] ?? null,
                'refresh_token' => null,
                'expires_at' => null,
                'metadata' => [
                    'team' => $data['team'] ?? null,
                    'authed_user' => $data['authed_user'] ?? null,
                    'scope' => $data['scope'] ?? null,
                    'bot_user_id' => $data['bot_user_id'] ?? null,
                ],
            ]
        );

        $frontendUrl = config('app.frontend_url', 'http://localhost:5173');
        return ['redirect' => $frontendUrl . '/integrations/slack/success'];
    }
}
