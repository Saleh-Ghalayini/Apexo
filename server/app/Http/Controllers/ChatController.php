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
        $validated = $request->validate([
            'session_id' => 'required|integer',
            'message' => 'required|string',
        ]);
        $result = $this->chatService->sendMessage($validated['session_id'], $validated['message']);
        return response()->json($result);
    }

    public function getMessages($sessionId)
    {
        // To be implemented
    }
}
