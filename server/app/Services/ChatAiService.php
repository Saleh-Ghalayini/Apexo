<?php

namespace App\Services;

use Prism\Prism\Prism;
use App\Models\ChatMessage;
use App\Models\ChatSession;
use Prism\Prism\ValueObjects\ToolResult;
use Prism\Prism\ValueObjects\Messages\UserMessage;
use Prism\Prism\ValueObjects\Messages\SystemMessage;
use Prism\Prism\ValueObjects\Messages\AssistantMessage;

class ChatAiService
{
    protected $promptService;
    protected $schemaService;
    protected $aiToolsService;
    protected $slackAnnouncementService;
    use \App\Traits\MessageAnalysisTrait;

    public function __construct(
        \App\Services\PromptService $promptService,
        \App\Services\SchemaService $schemaService,
        \App\Services\AIToolsService $aiToolsService,
        \App\Services\SlackAnnouncementService $slackAnnouncementService
    ) {
        $this->promptService = $promptService;
        $this->schemaService = $schemaService;
        $this->aiToolsService = $aiToolsService;
        $this->slackAnnouncementService = $slackAnnouncementService;
    }

    public function handleAiMessage(ChatSession $session, $user, string $userMessage, ChatMessage $userChatMessage): array
    {
        \Illuminate\Support\Facades\Log::info('[ChatAiService] handleAiMessage called', [
            'session_id' => $session->id,
            'user_id' => $user->id,
            'userMessage' => $userMessage
        ]);
        $messages = $this->getMessagesForAi($session, $user);
        $needsStructuredOutput = $this->messageNeedsStructuredOutput($userMessage);
        $tools = $this->aiToolsService->getToolsForUser($user);
        \Illuminate\Support\Facades\Log::info('[ChatAiService] Tools for user', [
            'tools' => array_map(function ($tool) {
                return method_exists($tool, 'getName') ? $tool->getName() : get_class($tool);
            }, $tools)
        ]);

        $hasToolCalls = false;
        $toolResults = [];
        $responseContent = '';
        $metadata = [];

        $request = Prism::text()
            ->using(config('prism.default_provider', 'openai'), 'gpt-4o')
            ->usingTemperature((float) config('prism.temperature', 0.7))
            ->withClientOptions(['timeout' => 60, 'connect_timeout' => 30])
            ->withMessages($messages);

        if (!empty($tools)) {
            $request->withTools($tools);
            $request->withMaxSteps(3);
        }

        $response = $request->asText();
        \Illuminate\Support\Facades\Log::info('[ChatAiService] Prism response', [
            'steps' => $response->steps,
            'text' => $response->text
        ]);

        if ($response->steps && count($response->steps) > 0) {
            foreach ($response->steps as $step) {
                if (!empty($step->toolCalls))
                    foreach ($step->toolCalls as $toolCall) {
                        $toolName = $toolCall->name;
                        $arguments = $toolCall->arguments();
                        $toolResultData = $this->aiToolsService->processToolCall($user, $toolName, $arguments);
                        $toolResult = new ToolResult(
                            $toolCall->id,
                            $toolName,
                            $arguments,
                            $toolResultData
                        );

                        $toolResults[] = $toolResult;
                        $hasToolCalls = true;
                    }
            }

            if ($hasToolCalls) {
                if ($needsStructuredOutput) {
                    $schema = $this->schemaService->getResponseSchema();
                    $finalRequest = Prism::text()
                        ->using(config('prism.default_provider', 'openai'), 'gpt-4o')
                        ->usingTemperature((float) config('prism.temperature', 0.7))
                        ->withClientOptions(['timeout' => 60, 'connect_timeout' => 30]);

                    $messagesWithToolResults = $messages;
                    $systemPrompt = array_shift($messagesWithToolResults);
                    $finalRequest->withSystemPrompt($systemPrompt->content);
                    $formattedMessages = [];
                    foreach ($messagesWithToolResults as $message) {
                        if ($message instanceof UserMessage)
                            $formattedMessages[] = new UserMessage($message->content);
                        else if ($message instanceof AssistantMessage)
                            $formattedMessages[] = new AssistantMessage($message->content);
                    }

                    $structurePrompt = "Based on the tool results, please provide the following information in a structured way:\n"
                        . "1. A helpful response to the user's query\n"
                        . "2. Any relevant tasks extracted from the data\n"
                        . "Tool results:\n";
                    foreach ($toolResults as $result)
                        $structurePrompt .= "- {$result->toolName}: " . json_encode($result->result) . "\n";
                    $formattedMessages[] = new UserMessage($structurePrompt);
                    $finalRequest->withMessages($formattedMessages);
                    $finalResponse = $finalRequest->asText();
                    $responseText = $finalResponse->text;
                    try {
                        $parsedData = $this->parseStructuredResponse($responseText);
                        $responseContent = $parsedData['response'] ?? $responseText;
                        $metadata = [
                            'tasks' => $parsedData['tasks'] ?? [],
                            'tool_results' => array_map(function ($result) {
                                return $result->result;
                            }, $toolResults)
                        ];
                    } catch (\Exception $parseException) {
                        $responseContent = $responseText;
                        $metadata = [
                            'tool_results' => array_map(function ($result) {
                                return $result->result;
                            }, $toolResults)
                        ];
                    }
                } else {
                    $finalRequest = Prism::text()
                        ->using(config('prism.default_provider', 'openai'), 'gpt-4o')
                        ->usingTemperature((float) config('prism.temperature', 0.7))
                        ->withClientOptions(['timeout' => 60, 'connect_timeout' => 30]);

                    $systemPrompt = array_shift($messages);
                    $finalRequest->withSystemPrompt($systemPrompt->content);
                    $formattedMessages = [];
                    foreach ($messages as $message) {
                        if ($message instanceof UserMessage)
                            $formattedMessages[] = new UserMessage($message->content);
                        else if ($message instanceof AssistantMessage)
                            $formattedMessages[] = new AssistantMessage($message->content);
                    }

                    $toolResultsPrompt = "Based on the following tool results, please provide a helpful response to the user's query:\n\n";
                    foreach ($toolResults as $result)
                        $toolResultsPrompt .= "- {$result->toolName}: " . json_encode($result->result) . "\n";
                    $formattedMessages[] = new UserMessage($toolResultsPrompt);
                    $finalRequest->withMessages($formattedMessages);
                    $finalResponse = $finalRequest->asText();
                    $responseContent = $finalResponse->text;

                    $metadata = [
                        'tool_results' => array_map(function ($result) {
                            return $result->result;
                        }, $toolResults)
                    ];
                }
            } else
                $responseContent = $response->text;
        } else {
            if ($needsStructuredOutput) {
                $schema = $this->schemaService->getResponseSchema();
                $structuredResponse = Prism::structured()
                    ->using(config('prism.default_provider', 'openai'), 'gpt-4o')
                    ->usingTemperature((float) config('prism.temperature', 0.7))
                    ->withClientOptions(['timeout' => 60, 'connect_timeout' => 30])
                    ->withMessages($messages)
                    ->withSchema($schema)
                    ->asStructured();

                $structuredData = $structuredResponse->structured;
                $responseContent = $structuredData['response'];
                $metadata = [
                    'tasks' => $structuredData['tasks'] ?? [],
                ];
            } else
                $responseContent = $response->text;
        }

        $aiChatMessage = new ChatMessage();
        $aiChatMessage->chat_session_id = $session->id;
        $aiChatMessage->role = 'assistant';
        $aiChatMessage->content = $responseContent;
        if (!empty($metadata))
            $aiChatMessage->metadata = json_encode($metadata);
        $aiChatMessage->save();

        if ($this->isSlackAnnouncement($responseContent, $metadata))
            if (preg_match('/(confirmed|sent to slack|has been sent|sending to slack|announcement sent|message sent|posted to slack|i will proceed to send|here\'s the content that will be sent|i will now send|i\'m sending|i am sending|i will send|sending now|will be sent|will now send|will send to slack|will send to the announcements channel|will send this to slack|will send this to the channel)/i', $responseContent)) {
                $slackData = $this->extractSlackAnnouncementData($responseContent, $metadata);
                if ($slackData && !empty($slackData['message']))    $this->slackAnnouncementService->sendSlackAnnouncement($user, $slackData);
            }

        return [
            'user_message' => $userChatMessage,
            'ai_message' => $aiChatMessage,
            'session' => $session,
        ];
    }

    private function getMessagesForAi(ChatSession $session, \App\Models\User $user = null): array
    {
        $messages = [
            new SystemMessage($this->promptService->getSystemPrompt($user))
        ];

        $recentMessages = $session->messages()
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->reverse();

        foreach ($recentMessages as $message)
            if ($message->role === 'user')
                $messages[] = new UserMessage($message->content);
            elseif ($message->role === 'assistant')
                $messages[] = new AssistantMessage($message->content);

        return $messages;
    }

    private function parseStructuredResponse(string $text): array
    {
        $result = [
            'response' => $text,
            'tasks' => [],
        ];

        if (preg_match('/```(?:json)?\s*([\s\S]+?)```/', $text, $matches))
            try {
                $jsonData = json_decode($matches[1], true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($jsonData)) {
                    $result['response'] = $jsonData['response'] ?? $result['response'];
                    $result['tasks'] = $jsonData['tasks'] ?? $result['tasks'];
                }
            } catch (\Exception $e) {
            }

        return $result;
    }

    private function isSlackAnnouncement(string $responseContent, array $metadata = []): bool
    {
        if (isset($metadata['slack_announcement']) && $metadata['slack_announcement'] === true)
            return true;

        if (stripos($responseContent, '[slack_announcement]') !== false)
            return true;

        $hasChannel = preg_match('/#\\w+|<!(channel|everyone|here)>/i', $responseContent);
        $hasAnnouncementKeyword = preg_match('/announcement|reminder|scheduled|progress|celebrate|prepare/i', $responseContent);
        if ($hasChannel && $hasAnnouncementKeyword)
            return true;

        return false;
    }

    private function extractSlackAnnouncementData(string $responseContent, array $metadata = []): ?array
    {
        if (isset($metadata['slack_announcement_data']))
            return $metadata['slack_announcement_data'];

        $channel = null;
        if (preg_match('/#(\w+)/', $responseContent, $matches))
            $channel = '#' . $matches[1];
        elseif (preg_match('/<!(channel|everyone|here)>/', $responseContent, $matches))
            $channel = $matches[0];

        if (!$channel)
            $channel = '#announcements';

        $message = null;
        if (preg_match('/content\s*:\s*([\s\S]+)/i', $responseContent, $matches))
            $message = trim($matches[1]);
        elseif (preg_match('/["“”](.*?)["“”]/s', $responseContent, $matches))
            $message = trim($matches[1]);
        elseif (preg_match('/(<!(channel|everyone|here)>.*)/i', $responseContent, $matches))
            $message = trim($matches[1]);

        if (!$message)  $message = trim($responseContent);

        $message = preg_replace('/^The announcement has been sent.*$/im', '', $message);
        $message = preg_replace('/^Would you like me to send.*$/im', '', $message);
        $message = preg_replace('/^If there\'s anything else.*$/im', '', $message);
        $message = trim($message);

        $title = 'Announcement';
        return [
            'title' => $title,
            'message' => $message,
            'slack_channel' => $channel,
        ];
    }
}
