<?php

namespace App\Services;

use App\Models\ChatMessage;
use App\Models\ChatSession;
use Illuminate\Support\Facades\Mail;

class ChatReportService
{
    protected $taskReportService;

    public function __construct(\App\Services\TaskReportService $taskReportService)
    {
        $this->taskReportService = $taskReportService;
    }

    public function isTaskReportRequest(string $userMessage): bool
    {
        $lowerMessage = strtolower($userMessage);
        return preg_match('/\\b(generate|create|make) (a )?(task )?report\\b/', $lowerMessage);
    }

    public function handleTaskReportRequest(ChatSession $session, ChatMessage $userChatMessage): array
    {
        $report = $this->taskReportService->generateTaskReport();
        return [
            'user_message' => $userChatMessage,
            'ai_message' => tap(new ChatMessage(), function ($aiChatMessage) use ($session, $report) {
                $aiChatMessage->chat_session_id = $session->id;
                $aiChatMessage->role = 'assistant';
                $aiChatMessage->content = $report;
                $aiChatMessage->save();
            }),
            'session' => $session,
        ];
    }

    public function isEmailReportRequest(string $userMessage): bool
    {
        $lowerMessage = strtolower($userMessage);
        return preg_match('/([a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+)/', $lowerMessage)
            && preg_match('/\\b(send|email)\\b/', $lowerMessage)
            && preg_match('/\\breport\\b/', $lowerMessage);
    }

    public function handleEmailReportRequest(ChatSession $session, ChatMessage $userChatMessage, string $userMessage): array
    {
        $lowerMessage = strtolower($userMessage);
        preg_match('/([a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+)/', $lowerMessage, $emailMatch);
        $toEmail = $emailMatch[1];
        $lastReport = $session->messages()->where('role', 'assistant')
            ->where('content', 'like', '%TASK REPORT%')
            ->orderBy('created_at', 'desc')->first();

        if (!$lastReport)
            return [
                'user_message' => $userChatMessage,
                'ai_message' => tap(new ChatMessage(), function ($aiChatMessage) use ($session) {
                    $aiChatMessage->chat_session_id = $session->id;
                    $aiChatMessage->role = 'assistant';
                    $aiChatMessage->content = "No report found to send. Please generate a report first.";
                    $aiChatMessage->save();
                }),
                'session' => $session,
            ];

        $subject = 'Task Progress Report';
        if (preg_match('/subject:?\s*([\w\s]+)/i', $userMessage, $subjectMatch))
            $subject = trim($subjectMatch[1]);
        elseif (preg_match('/title:?\s*([\w\s]+)/i', $userMessage, $titleMatch))
            $subject = trim($titleMatch[1]);

        $fromEmail = config('mail.from.address', 'ghalayinisaleh4@gmail.com');
        Mail::raw($lastReport->content, function ($message) use ($fromEmail, $toEmail, $subject) {
            $message->from($fromEmail)
                ->to($toEmail)
                ->subject($subject);
        });

        return [
            'user_message' => $userChatMessage,
            'ai_message' => tap(new ChatMessage(), function ($aiChatMessage) use ($session, $toEmail, $subject) {
                $aiChatMessage->chat_session_id = $session->id;
                $aiChatMessage->role = 'assistant';
                $aiChatMessage->content = "Report sent to $toEmail with subject '$subject'.";
                $aiChatMessage->save();
            }),
            'session' => $session,
        ];
    }
}
