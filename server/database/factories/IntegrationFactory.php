<?php

namespace Database\Factories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Integration>
 */
class IntegrationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $provider = fake()->randomElement([
            'notion',
            'slack',
            'google_calendar',
            'google_meet',
            'gmail'
        ]);

        return [
            'company_id' => Company::factory(),
            'provider' => $provider,
            'settings' => $this->fakeSettings($provider),
        ];
    }

    private function fakeSettings(string $provider): array
    {
        return match ($provider) {
            'notion' => [
                'api_token' => fake()->uuid(),
                'workspace' => fake()->word(),
            ],
            'slack' => [
                'webhook_url' => fake()->url(),
                'channel' => '#' . fake()->word(),
            ],
            'google_calendar', 'google_meet', 'gmail' => [
                'access_token' => fake()->uuid(),
                'refresh_token' => fake()->uuid(),
                'expires_at' => now()->addHour(),
            ],
            default => [],
        };
    }
}
