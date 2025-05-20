<?php

namespace App\Jobs;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendTaskDeadlineReminderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $taskId;

    public function __construct($taskId)
    {
        $this->taskId = $taskId;
    }

    public function handle()
    {
        $task = Task::with('user')->find($this->taskId);
        if (!$task || !$task->user || !$task->user->email)  return;

        $user = $task->user;
        $payload = [
            'user_name' => $user->name,
            'user_email' => $user->email,
            'task_title' => $task->title,
            'task_details' => $task->description,
            'deadline' => $task->deadline ? $task->deadline->toDateTimeString() : null,
        ];
        $temperature = config('services.openai.temperature', 0.7);
        if (is_string($temperature)) {
            $temperature = (float) $temperature;
        }
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . config('services.openai.secret'),
        ])->post(config('services.openai.url', 'https://api.openai.com/v1') . '/chat/completions', [
            'model' => config('services.openai.model', 'gpt-4o'),
            'messages' => [
                ['role' => 'system', 'content' => 'You are an assistant that writes professional emails.'],
                ['role' => 'user', 'content' => "Generate a friendly and professional email reminder for the following user and task.\nUser Name: {$user->name}\nUser Email: {$user->email}\nTask Title: {$task->title}\nTask Details: {$task->description}\nDeadline: {$payload['deadline']}\nReturn a JSON object with 'subject' and 'body'."],
            ],
            'max_tokens' => 400,
            'temperature' => $temperature,
        ]);
        if ($response->failed())    return;

        $choices = $response->json('choices');
        $content = $choices[0]['message']['content'] ?? '';
        $json = null;

        if (preg_match('/```json(.*?)```/s', $content, $matches))   $json = trim($matches[1]);
        elseif (preg_match('/\{.*\}/s', $content, $matches))    $json = $matches[0];
        $email = json_decode($json, true);

        if (!$email || !isset($email['subject']) || !isset($email['body']))   return;

        Mail::raw($email['body'], function ($message) use ($user, $email) {
            $message->to($user->email)
                ->subject($email['subject'] ?? 'Task Deadline Reminder');
        });
    }
}
