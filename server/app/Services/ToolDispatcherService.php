<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class ToolDispatcherService
{
    protected array $tools;

    public function __construct(
        ChatReportService $chatReportService,
        ChatAiService $chatAiService,
        AIToolsService $aiToolsService,
        AIService $aiService
    ) {
        $this->tools = [
            'chat_report' => $chatReportService,
            'chat_ai' => $chatAiService,
            'ai_tools' => $aiToolsService,
            'ai_service' => $aiService,
        ];
    }

    public function dispatchTool($session, $user, $userMessage, $userChatMessage)
    {
        if ($this->tools['chat_report']->isTaskReportRequest($userMessage)) {
            Log::info('[ToolDispatcher] Task report request detected');
            return [
                'result' => $this->tools['chat_report']->handleTaskReportRequest($session, $userChatMessage),
                'tool' => 'task_report',
                'handled' => true,
            ];
        }
        if ($this->tools['chat_report']->isEmailReportRequest($userMessage)) {
            Log::info('[ToolDispatcher] Email report request detected');
            return [
                'result' => $this->tools['chat_report']->handleEmailReportRequest($session, $userChatMessage, $userMessage),
                'tool' => 'email_report',
                'handled' => true,
            ];
        }

        if (preg_match('/analyz(e|ing|ed)? (the )?meeting/i', $userMessage) && preg_match('/(report|download|pdf|excel)/i', $userMessage)) {
            Log::info('[ToolDispatcher] Analytics+report request detected');
            if (preg_match('/meeting (titled|named)?[\s\'"]*([\w\s\-]+)[\'"]*/i', $userMessage, $matches)) {
                $meetingTitle = trim($matches[2]);
                Log::info('[ToolDispatcher] Extracted meeting title', ['meetingTitle' => $meetingTitle]);
                $meeting = \App\Models\Meeting::where('title', $meetingTitle)->latest()->first();
                if ($meeting) {
                    Log::info('[ToolDispatcher] Found meeting', ['meeting_id' => $meeting->id]);
                } else {
                    Log::warning('[ToolDispatcher] Meeting not found', ['meetingTitle' => $meetingTitle]);
                }
                if ($meeting && $meeting->analytics) {
                    Log::info('[ToolDispatcher] Analytics are ready for meeting', ['meeting_id' => $meeting->id]);
                    $format = (stripos($userMessage, 'excel') !== false || stripos($userMessage, 'xlsx') !== false) ? 'xlsx' : 'pdf';
                    Log::info('[ToolDispatcher] Generating report', ['format' => $format]);
                    $path = $this->tools['ai_service']->generateMeetingReport($meeting, $format);
                    Log::info('[ToolDispatcher] Report generated', ['path' => $path]);
                    $filename = basename($path);
                    $mime = $format === 'pdf' ? 'application/pdf' : 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
                    $fileContent = \Illuminate\Support\Facades\Storage::get($path);
                    $base64 = base64_encode($fileContent);
                    $aiMessage = tap(new \App\Models\ChatMessage(), function ($aiChatMessage) use ($session) {
                        $aiChatMessage->chat_session_id = $session->id;
                        $aiChatMessage->role = 'assistant';
                        $aiChatMessage->content = 'The report got generated and downloaded on your machine.';
                        $aiChatMessage->save();
                    });
                    $aiMessage->metadata = json_encode([
                        'tool_results' => [[
                            'file' => [
                                'name' => $filename,
                                'mime' => $mime,
                                'base64' => $base64,
                            ]
                        ]]
                    ]);
                    $aiMessage->save();
                    Log::info('[ToolDispatcher] AI message with file sent', ['filename' => $filename]);
                    return [
                        'result' => [
                            'user_message' => $userChatMessage,
                            'ai_message' => $aiMessage,
                            'session' => $session,
                        ],
                        'tool' => 'meeting_report',
                        'handled' => true,
                    ];
                } else {
                    Log::info('[ToolDispatcher] Analytics not ready for meeting', ['meetingTitle' => $meetingTitle]);
                    $aiMessage = tap(new \App\Models\ChatMessage(), function ($aiChatMessage) use ($session, $meetingTitle) {
                        $aiChatMessage->chat_session_id = $session->id;
                        $aiChatMessage->role = 'assistant';
                        $aiChatMessage->content = "I'm preparing the analytics for the meeting titled '$meetingTitle'. Please try again in a few moments, and I'll have your report ready for download.";
                        $aiChatMessage->save();
                    });
                    return [
                        'result' => [
                            'user_message' => $userChatMessage,
                            'ai_message' => $aiMessage,
                            'session' => $session,
                        ],
                        'tool' => 'meeting_report_pending',
                        'handled' => true,
                    ];
                }
            } else {
                Log::warning('[ToolDispatcher] Could not extract meeting title', ['userMessage' => $userMessage]);
            }
        }
        Log::info('[ToolDispatcher] Passing to chatAiService.handleAiMessage');
        return [
            'result' => $this->tools['chat_ai']->handleAiMessage($session, $user, $userMessage, $userChatMessage),
            'tool' => 'chat_ai',
            'handled' => false,
        ];
    }
}
