<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class MeetingFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'title' => $this->faker->sentence(3),
            'scheduled_at' => $this->faker->dateTimeBetween('-1 week', '+1 week'),
            'ended_at' => $this->faker->dateTimeBetween('+1 week', '+2 weeks'),
            'transcript' => $this->faker->paragraph(),
            'summary' => $this->faker->paragraph(),
            'status' => 'scheduled',
            'external_id' => null,
            'meeting_url' => $this->faker->url(),
            'attendees' => [],
            'metadata' => [],
            'analytics' => null,
        ];
    }
}
