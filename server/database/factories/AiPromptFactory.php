<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class AiPromptFactory extends Factory
{
    public function definition(): array
    {
        $promptTypes = [
            'meeting_summary' => 'Summarize the key points from this meeting...',
            'task_extraction' => 'Extract all tasks and action items from this conversation...',
            'meeting_agenda' => 'Create a structured agenda for a meeting about...',
            'email_draft' => 'Draft a professional email to...',
            'report_generation' => 'Generate a detailed report based on the following data...',
            'question_answering' => 'Based on the company data, answer the following question...'
        ];

        $promptType = array_rand($promptTypes);
        $promptTemplate = $promptTypes[$promptType];

        return [
            'title' => ucwords(str_replace('_', ' ', $promptType)),
            'description' => fake()->sentence(),
            'prompt_type' => $promptType,
            'prompt_template' => $promptTemplate . fake()->paragraph(),
            'system_instructions' => fake()->paragraph(3),
            'is_system' => fake()->boolean(30),
            'metadata' => json_encode([
                'model' => fake()->randomElement(['gpt-4-turbo', 'gpt-4', 'claude-3-opus']),
                'temperature' => fake()->randomFloat(1, 0, 1),
                'max_tokens' => fake()->randomElement([1000, 2000, 4000]),
            ]),
        ];
    }
}
