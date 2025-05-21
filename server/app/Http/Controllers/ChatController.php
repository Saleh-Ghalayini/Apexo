<?php

namespace App\Http\Controllers;

use App\Services\ChatService;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    protected $chatService;

    public function __construct(ChatService $chatService)
    {
        $this->chatService = $chatService;
    }

    public function getSessions(Request $request)
    {
        $status = $request->query('status');
        $sessions = $this->chatService->getSessions($status);

        return response()->json([
            'success' => true,
            'payload' => $sessions
        ]);
    }

    public function getSession($id, Request $request)
    {
        $limit = $request->query('limit');
        $since = $request->query('since');

        $session = $this->chatService->getSession($id, $limit, $since);

        return response()->json([
            'success' => true,
            'payload' => $session
        ]);
    }

    public function createSession(Request $request)
    {
        $title = $request->input('title');
        $initialMessage = $request->input('initial_message');
        try {
            $result = $this->chatService->createSession($title, $initialMessage);
            return response()->json([
                'success' => true,
                'payload' => $result
            ], 201);
        } catch (\Throwable $e) {
            \Log::error('Chat session creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function sendMessage($id, Request $request)
    {
        $message = $request->input('message');
        try {
            $result = $this->chatService->sendMessage($id, $message);
            return response()->json([
                'success' => true,
                'payload' => $result
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function archiveSession($id)
    {
        try {
            $session = $this->chatService->archiveSession($id);
            return response()->json([
                'success' => true,
                'payload' => $session
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function deleteSession($id)
    {
        try {
            $result = $this->chatService->deleteSession($id);
            return response()->json([
                'success' => true,
                'payload' => ['deleted' => $result]
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
