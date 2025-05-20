<?php

namespace Database\Factories;

use App\Models\IntegrationCredential;
use App\Models\User;
use App\Models\Integration;
use Illuminate\Database\Eloquent\Factories\Factory;

class IntegrationCredentialFactory extends Factory
{
    protected $model = IntegrationCredential::class;

    public function definition(): array
    {
        return [
            'integration_id' => Integration::factory(),
            'user_id' => User::factory(),
            'type' => 'slack',
            'access_token' => $this->faker->sha256,
            'refresh_token' => $this->faker->sha256,
            'expires_at' => $this->faker->dateTimeBetween('+1 month', '+1 year'),
            'metadata' => [],
        ];
    }
}
