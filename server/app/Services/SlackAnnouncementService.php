<?php

namespace App\Services;

use App\Models\Announcement;
use Illuminate\Support\Facades\Http;
use App\Models\IntegrationCredential;

class SlackAnnouncementService
{
    public function sendSlackAnnouncement($user, array $slackData)
    {
        if (empty($slackData['title']) || empty($slackData['message']) || empty($slackData['slack_channel']))
            return;

        $credential = IntegrationCredential::where('user_id', $user->id)
            ->where('type', 'slack')
            ->first();
        if (!$credential)   return;

        $accessToken = $credential->access_token;
        $response = Http::withToken($accessToken)
            ->post('https://slack.com/api/chat.postMessage', [
                'channel' => $slackData['slack_channel'],
                'text' => $slackData['message'],
            ]);
        $data = $response->json();
        if (empty($data['ok'])) return;

        Announcement::create([
            'title' => $slackData['title'],
            'message' => $slackData['message'],
            'slack_channel' => $slackData['slack_channel'],
            'user_id' => $user->id,
        ]);
    }
}
