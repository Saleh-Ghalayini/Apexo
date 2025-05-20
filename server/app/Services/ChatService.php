<?php

namespace App\Services;

class ChatService
{
    protected \App\Services\ChatMessageService $chatMessageService;
    protected \App\Services\ChatReportService $chatReportService;
    protected \App\Services\ChatAiService $chatAiService;
    protected \App\Services\ToolDispatcherService $toolDispatcherService;

    public function __construct(
        \App\Services\ChatMessageService $chatMessageService,
        \App\Services\ChatReportService $chatReportService,
        \App\Services\ChatAiService $chatAiService,
        \App\Services\ToolDispatcherService $toolDispatcherService
    ) {
        $this->chatMessageService = $chatMessageService;
        $this->chatReportService = $chatReportService;
        $this->chatAiService = $chatAiService;
        $this->toolDispatcherService = $toolDispatcherService;
    }

    public function startChatSession(array $participants): array
    {
        $session = [];
        $session['id'] = uniqid('chat_', true);
        $session['participants'] = $participants;
        return $session;
    }

    public function sendMessage(array $session, string $message, string $sender): array
    {
        $msg = [];
        $msg['content'] = $message;
        $msg['sender'] = $sender;
        $msg['timestamp'] = time();
        $this->chatMessageService->saveMessage($session['id'], $msg);

        if ($sender !== 'ai') {
            if (str_starts_with($message, '/tool ')) {
                $toolCommand = substr($message, 6);
                $toolResult = $this->toolDispatcherService->dispatch($session, $toolCommand, $sender);
                $this->chatMessageService->saveMessage($session['id'], [
                    'content' => $toolResult,
                    'sender' => 'tool',
                    'timestamp' => time()
                ]);
            } else {
                $aiResponse = $this->chatAiService->generateResponse($session, $message, $sender);
                $this->chatMessageService->saveMessage($session['id'], $aiResponse);
            }
        }

        $this->chatReportService->updateReport($session['id']);

        return $msg;
    }
}
