<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ChatSession>
 */
class ChatSessionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'summary' => $this->faker->sentence(10),
            'messages' => json_encode([
                [
                    'sender' => 'user',
                    'message' => $this->faker->sentence(),
                    'timestamp' => now()->subMinutes(2)->toIso8601String()
                ],
                [
                    'sender' => 'apexo',
                    'message' => $this->faker->sentence(),
                    'timestamp' => now()->subMinute()->toIso8601String()
                ],
            ]),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
