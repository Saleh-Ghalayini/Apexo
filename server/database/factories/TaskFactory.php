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

        $meetingId = fake()->optional(0.3)->randomElement([
            function () {
                return \App\Models\Meeting::factory()->create()->id;
            },
            null
        ]);

        if (is_callable($meetingId))    $meetingId = $meetingId();

        return [
            'user_id' => \App\Models\User::factory(),
            'meeting_id' => $meetingId,
            'title' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'status' => $status,
            'priority' => fake()->randomElement(['low', 'medium', 'high', 'urgent']),
            'due_date' => $dueDate,
            'completed_at' => $completedAt,
            'assignee_name' => fake()->optional(0.7)->name(),
            'assignee_email' => fake()->optional(0.7)->email(),
            'external_id' => fake()->optional(0.5)->uuid(),
            'external_url' => fake()->optional(0.4)->url(),
            'metadata' => json_encode([
                'source' => fake()->randomElement(['meeting', 'chat', 'manual', 'integration']),
                'labels' => fake()->optional(0.6)->randomElements(['bug', 'feature', 'documentation', 'design', 'research'], fake()->numberBetween(0, 3)),
            ]),
        ];
    }
}
