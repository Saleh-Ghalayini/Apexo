<?php

namespace App\Http\Controllers;

use App\Services\ChatService;

class ChatController extends Controller
{
    protected $chatService;

    public function __construct(ChatService $chatService)
    {
        $this->chatService = $chatService;
    }

    public function getSessions($request)
    {
        // To be implemented
    }

    public function getSession($id, $request)
    {
        // To be implemented
    }

    public function createSession($request)
    {
        // To be implemented
    }

    public function sendMessage($id, $request)
    {
        // To be implemented
    }

    public function archiveSession($id)
    {
        // To be implemented
    }

    public function deleteSession($id)
    {
        // To be implemented
    }
}
