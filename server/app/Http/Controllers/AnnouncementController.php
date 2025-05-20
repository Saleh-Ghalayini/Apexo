<?php

namespace App\Http\Controllers;

use App\Services\AnnouncementService;

class AnnouncementController extends Controller
{
    use ResponseTrait;

    protected AnnouncementService $announcementService;

    public function __construct(AnnouncementService $announcementService)
    {
        $this->announcementService = $announcementService;
    }

    public function sendToSlack($request) {}
}
