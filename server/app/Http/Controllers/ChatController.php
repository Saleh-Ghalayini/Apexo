<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ChatController extends Controller
{
    protected $chatService;

    public function __construct(\App\Services\ChatService $chatService)
    {
        $this->chatService = $chatService;
    }

    public function sendMessage(Request $request)
    {
        // To be implemented
    }

    public function getMessages($sessionId)
    {
        // To be implemented
    }
}
