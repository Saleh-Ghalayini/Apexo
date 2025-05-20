<?php

namespace App\Services;

use App\Models\ChatSession;
use App\Services\ChatAiService;
use App\Services\ChatReportService;
use App\Traits\MessageAnalysisTrait;
use Illuminate\Support\Facades\Auth;
use App\Services\ChatMessageService;

class ChatService
{
    use MessageAnalysisTrait;

    protected ChatMessageService $chatMessageService;
    protected ChatReportService $chatReportService;
    protected ChatAiService $chatAiService;
    protected \App\Services\ToolDispatcherService $toolDispatcherService;

    public function __construct(
        ChatMessageService $chatMessageService,
        ChatReportService $chatReportService,
        ChatAiService $chatAiService,
        \App\Services\ToolDispatcherService $toolDispatcherService
    ) {
        $this->chatMessageService = $chatMessageService;
        $this->chatReportService = $chatReportService;
        $this->chatAiService = $chatAiService;
        $this->toolDispatcherService = $toolDispatcherService;
    }

    public function createSession(?string $title = null, ?string $initialMessage = null): array
    {
        $user = Auth::user();
        $session = new ChatSession();
        $session->user_id = $user->id;
        $session->title = $title ?? 'New Chat';
        $session->status = 'active';
        $session->last_activity_at = now();
        $session->save();

        if ($initialMessage)
            return [
                'session' => $session,
                'messages' => [
                    $this->sendMessage($session->id, $initialMessage)['user_message'],
                    $this->sendMessage($session->id, $initialMessage)['ai_message']
                ],
            ];

        return [
            'session' => $session,
            'messages' => [],
        ];
    }

    public function getSessions(?string $status = null)
    {
        $query = ChatSession::where('user_id', Auth::id())
            ->orderBy('last_activity_at', 'desc');

        if ($status)    $query->where('status', $status);

        return $query->get();
    }

    public function getSession(int $sessionId, ?int $limit = null, ?string $since = null)
    {
        $session = ChatSession::where('id', $sessionId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $messagesQuery = $session->messages()->orderBy('created_at', 'asc');

        if ($since)
            $messagesQuery->where('created_at', '>', $since);
        if ($limit)
            $messagesQuery->limit($limit);

        $session->setRelation('messages', $messagesQuery->get());

        return $session;
    }

    public function sendMessage(int $sessionId, string $userMessage): array
    {
        $session = $this->getSession($sessionId);
        $user = Auth::user();
        $session->last_activity_at = now();
        $session->save();

        $userChatMessage = $this->chatMessageService->persistUserMessage($session, $userMessage);
        $this->chatMessageService->updateSessionTitleIfNeeded($session, $userMessage, $userChatMessage);

        try {
            $dispatchResult = $this->toolDispatcherService->dispatchTool($session, $user, $userMessage, $userChatMessage);
            if ($dispatchResult['handled'])
                return $dispatchResult['result'];


            return $dispatchResult['result'];
        } catch (\Throwable $e) {
            $errorMsg = $e->getMessage() ?: 'An unknown error occurred.';

            $aiErrorMessage = tap(new \App\Models\ChatMessage(), function ($aiChatMessage) use ($session, $errorMsg) {
                $aiChatMessage->chat_session_id = $session->id;
                $aiChatMessage->role = 'assistant';
                $aiChatMessage->content = "Sorry, I couldn't complete your request due to: $errorMsg. Please try again or ask for help.";
                $aiChatMessage->save();
            });
            return [
                'user_message' => $userChatMessage,
                'ai_message' => $aiErrorMessage,
                'session' => $session,
                'error' => $errorMsg,
            ];
        }
    }

    public function archiveSession(int $sessionId): ChatSession
    {
        $session = $this->getSession($sessionId);
        $session->status = 'archived';
        $session->save();

        return $session;
    }

    public function deleteSession(int $sessionId): bool
    {
        $session = $this->getSession($sessionId);
        $session->messages()->delete();
        return $session->delete();
    }
}
