<?php

namespace Database\Factories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Meeting>
 */
class MeetingFactory extends Factory
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
            'transcript' => fake()->optional()->paragraphs(3, true),
            'summary' => fake()->optional()->sentence(),
            'scheduled_at' => fake()->dateTimeBetween('now', '+1 month'),
        ];
    }
}
