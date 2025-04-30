<?php

namespace Database\Factories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Report>
 */
class ReportFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'type' => fake()->randomElement(['weekly', 'monthly', 'yearly']),
            'data' => [
                'summary' => fake()->sentence(),
                'performance_score' => fake()->numberBetween(60, 100),
                'highlights' => fake()->sentences(3),
                'issues' => fake()->optional()->sentences(2),
            ],
            'generated_at' => fake()->dateTimeBetween('-1 month', 'now'),
        ];
    }
}
