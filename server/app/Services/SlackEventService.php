<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class SlackEventService
{
    public function handleUrlVerification($data)
    {
        return $data['challenge'] ?? '';
    }

    public function handleEvent($event)
    {
        if ((($event['type'] ?? null) === 'app_mention' || ($event['type'] ?? null) === 'message') && empty($event['bot_id'])) {
            Log::info('Received Slack event', $event);
        }
    }
}
