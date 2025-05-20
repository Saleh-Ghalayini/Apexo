<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ChatMessageFactory extends Factory
{
    public function definition(): array
    {
        return [
            'chat_session_id' => \App\Models\ChatSession::factory(),
            'role' => fake()->randomElement(['user', 'assistant']),
            'content' => fake()->paragraph(fake()->numberBetween(1, 4)),
            'metadata' => json_encode([
                'processed' => fake()->boolean(80),
                'tokens' => fake()->numberBetween(10, 200),
                'entities' => fake()->optional(0.3)->randomElements(['task', 'meeting', 'person', 'date'], fake()->numberBetween(0, 2)),
            ]),
        ];
    }
}
