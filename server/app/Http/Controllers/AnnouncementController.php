<?php

namespace App\Http\Controllers;

use App\Traits\ResponseTrait;
use App\Services\AnnouncementService;
use App\Http\Requests\SendAnnouncementRequest;

class AnnouncementController extends Controller
{
    use ResponseTrait;
    protected AnnouncementService $announcementService;

    public function __construct(AnnouncementService $announcementService)
    {
        $this->announcementService = $announcementService;
    }

    public function sendToSlack(SendAnnouncementRequest $request)
    {
        $user = $request->user();
        $data = $request->validated();
        $result = $this->announcementService->sendToSlack($user, $data);
        if (!empty($result['success'])) return $this->successResponse($result['announcement']);
        else return $this->errorResponse($result['error'] ?? 'Failed', $result['code'] ?? 400);
    }
}
