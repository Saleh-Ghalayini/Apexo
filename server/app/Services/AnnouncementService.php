<?php

namespace App\Services;

use App\Models\User;
use App\Models\Announcement;
use Illuminate\Support\Facades\Http;
use App\Models\IntegrationCredential;

class AnnouncementService
{
    public function sendToSlack(User $user, array $data): array
    {
        $credential = IntegrationCredential::where('user_id', $user->id)
            ->where('type', 'slack')
            ->first();

        if (!$credential)
            return [
                'success' => false,
                'error' => 'Slack not connected',
                'code' => 400
            ];

        $accessToken = $credential->access_token;

        $response = Http::withToken($accessToken)
            ->post('https://slack.com/api/chat.postMessage', [
                'channel' => $data['slack_channel'],
                'text' => $data['message'],
            ]);

        $slackData = $response->json();

        if (empty($slackData['ok']))
            return [
                'success' => false,
                'error' => 'Slack API error',
                'details' => $slackData,
                'code' => 400
            ];

        $announcement = Announcement::create([
            'title' => $data['title'],
            'message' => $data['message'],
            'slack_channel' => $data['slack_channel'],
            'user_id' => $user->id,
        ]);

        return [
            'success' => true,
            'announcement' => $announcement
        ];
    }
}
