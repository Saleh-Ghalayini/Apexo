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

        return response()->json($sessions);
    }

    public function getSession($id, $request)
    {
        $limit = $request->query('limit');
        $since = $request->query('since');

        $session = $this->chatService->getSession($id, $limit, $since);

        return response()->json($session);
    }

    public function createSession($request)
    {
        $title = $request->input('title');
        $initialMessage = $request->input('initial_message');

        $result = $this->chatService->createSession($title, $initialMessage);

        return response()->json($result, 201);
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
