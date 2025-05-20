<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\ChatSession;
use App\Services\ChatService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;

class TestAIChatCommand extends Command
{
    protected $signature = 'chat:test-ai {email} {message}';
    protected $description = 'Test the AI chat functionality with database access';

    public function handle(ChatService $chatService)
    {
        $email = $this->argument('email');
        $message = $this->argument('message');

        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("User with email {$email} not found.");
            return 1;
        }

        $this->info("Testing AI chat as user: {$user->name} ({$user->role})");
        $this->info("Message: {$message}");
        $this->newLine();

        Auth::login($user);

        $session = new ChatSession();
        $session->user_id = $user->id;
        $session->title = 'AI Database Access Test';
        $session->status = 'active';
        $session->last_activity_at = now();
        $session->save();

        $this->info("Sending message and waiting for response...");
        $this->newLine();

        $result = $chatService->sendMessage($session->id, $message);

        if (isset($result['error'])) {
            $this->error("Error: {$result['error']}");
            return 1;
        }

        $this->info("AI Response:");
        $this->newLine();
        $this->line($result['ai_message']->content);
        $this->newLine();

        if (!empty($result['ai_message']->metadata)) {
            $this->info("Metadata:");
            $this->newLine();
            $this->line(json_encode($result['ai_message']->metadata, JSON_PRETTY_PRINT));
        }

        return 0;
    }
}
