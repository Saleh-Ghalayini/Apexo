<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CompanyFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->company();
        $domain = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $name)) . '.com';

        return [
            'name' => $name,
            'domain' => $domain,
            'address' => fake()->address(),
            'city' => fake()->city(),
            'state' => fake()->state(),
            'zip_code' => fake()->postcode(),
            'country' => fake()->country(),
            'phone' => fake()->phoneNumber(),
            'website' => 'https://' . $domain,
        ];
    }
}
