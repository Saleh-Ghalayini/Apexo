<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ChatSessionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'title' => fake()->optional(0.7)->sentence(3),
            'status' => fake()->randomElement(['active', 'archived']),
            'last_activity_at' => fake()->dateTimeBetween('-30 days', 'now'),
        ];
    }
}
