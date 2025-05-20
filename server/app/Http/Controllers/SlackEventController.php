<?php

namespace App\Http\Controllers;

use App\Traits\ResponseTrait;
use App\Services\SlackEventService;
use App\Http\Requests\SlackEventRequest;

class SlackEventController extends Controller
{
    use ResponseTrait;
    protected SlackEventService $slackEventService;

    public function __construct(SlackEventService $slackEventService)
    {
        $this->slackEventService = $slackEventService;
    }

    public function handle(SlackEventRequest $request)
    {
        $data = $request->all();

        if (isset($data['type']) && $data['type'] === 'url_verification') {
            $challenge = $this->slackEventService->handleUrlVerification($data);
            return response($challenge, 200);
        }

        if (isset($data['event'])) $this->slackEventService->handleEvent($data['event']);

        return response('OK', 200);
    }
}
