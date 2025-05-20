<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class IntegrationFactory extends Factory
{
    public function definition(): array
    {
        $provider = fake()->randomElement(['google_calendar', 'slack', 'microsoft_teams', 'jira', 'trello']);
        $expiresAt = fake()->dateTimeBetween('+1 day', '+60 days');

        return [
            'user_id' => \App\Models\User::factory(),
            'provider' => $provider,
            'token_type' => 'Bearer',
            'access_token' => fake()->sha256(),
            'refresh_token' => fake()->sha256(),
            'expires_at' => $expiresAt,
            'status' => 'active',
            'metadata' => json_encode([
                'scope' => $provider === 'google_calendar' ?
                    'https://www.googleapis.com/auth/calendar' :
                    'read write',
                'account_email' => fake()->email(),
            ]),
        ];
    }
}
