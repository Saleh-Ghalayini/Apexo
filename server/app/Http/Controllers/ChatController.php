<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use App\Services\ChatService;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\SendChatMessageRequest;
use App\Http\Requests\CreateChatSessionRequest;

class ChatController extends Controller
{
    use ResponseTrait;
    protected $chatService;

    public function __construct(ChatService $chatService)
    {
        $this->chatService = $chatService;
    }

    public function getSessions(Request $request): JsonResponse
    {
        $status = $request->query('status');
        $sessions = $this->chatService->getSessions($status);

        return $this->successResponse($sessions);
    }

    public function getSession(int $id, Request $request): JsonResponse
    {
        $limit = $request->query('limit');
        $since = $request->query('since');

        $session = $this->chatService->getSession($id, $limit, $since);

        return $this->successResponse($session);
    }

    public function createSession(CreateChatSessionRequest $request): JsonResponse
    {
        $title = $request->input('title');
        $initialMessage = $request->input('initial_message');

        $result = $this->chatService->createSession($title, $initialMessage);

        return $this->successResponse($result, 201);
    }

    public function sendMessage(int $id, SendChatMessageRequest $request): JsonResponse
    {
        $message = $request->input('message');
        $result = $this->chatService->sendMessage($id, $message);

        return $this->successResponse($result);
    }

    public function archiveSession(int $id): JsonResponse
    {
        $session = $this->chatService->archiveSession($id);

        return $this->successResponse($session);
    }

    public function deleteSession(int $id): JsonResponse
    {
        $result = $this->chatService->deleteSession($id);

        return $this->successResponse(['deleted' => $result]);
    }
}
