<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    public function definition(): array
    {
        $dueDate = fake()->optional(0.8)->dateTimeBetween('-5 days', '+30 days');
        $status = fake()->randomElement(['todo', 'in_progress', 'blocked', 'review', 'completed']);
        $completedAt = $status === 'completed' ? fake()->dateTimeBetween('-30 days', 'now') : null;

        return [
            'user_id' => \App\Models\User::factory(),
            'meeting_id' => null,
            'title' => fake()->sentence(),
            'status' => $status,
            'priority' => fake()->randomElement(['low', 'medium', 'high', 'urgent']),
            'due_date' => $dueDate,
            'completed_at' => $completedAt,
        ];
    }
}
