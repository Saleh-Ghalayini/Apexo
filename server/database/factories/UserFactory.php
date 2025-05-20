<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'company_id' => \App\Models\Company::factory(),
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'role' => fake()->randomElement(['employee', 'manager', 'hr']),
            'job_title' => fake()->jobTitle(),
            'department' => fake()->randomElement(['Engineering', 'Marketing', 'Sales', 'HR', 'Finance', 'Operations']),
            'phone' => fake()->phoneNumber(),
            'avatar' => null,
            'active' => true,
            'remember_token' => Str::random(10),
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn(array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function employee(): static
    {
        return $this->state(fn(array $attributes) => [
            'role' => 'employee',
        ]);
    }

    public function manager(): static
    {
        return $this->state(fn(array $attributes) => [
            'role' => 'manager',
        ]);
    }

    public function hr(): static
    {
        return $this->state(fn(array $attributes) => [
            'role' => 'hr',
        ]);
    }
}
