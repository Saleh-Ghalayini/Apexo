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
        $status = $request->query('status');
        $sessions = $this->chatService->getSessions($status);

        return response()->json([
            'success' => true,
            'payload' => $sessions
        ]);
    }

    public function getSession($id, $request)
    {
        $limit = $request->query('limit');
        $since = $request->query('since');

        $session = $this->chatService->getSession($id, $limit, $since);

        return response()->json([
            'success' => true,
            'payload' => $session
        ]);
    }

    public function createSession($request)
    {
        $title = $request->input('title');
        $initialMessage = $request->input('initial_message');

        $result = $this->chatService->createSession($title, $initialMessage);

        // Always wrap in { success, payload } for API consistency
        return response()->json([
            'success' => true,
            'payload' => $result
        ], 201);
    }

    public function sendMessage($id, $request)
    {
        $message = $request->input('message');
        $result = $this->chatService->sendMessage($id, $message);

        return response()->json([
            'success' => true,
            'payload' => $result
        ]);
    }

    public function archiveSession($id)
    {
        $session = $this->chatService->archiveSession($id);

        return response()->json([
            'success' => true,
            'payload' => $session
        ]);
    }

    public function deleteSession($id)
    {
        $result = $this->chatService->deleteSession($id);

        return response()->json([
            'success' => true,
            'payload' => ['deleted' => $result]
        ]);
    }
}
