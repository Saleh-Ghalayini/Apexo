<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\Company;

class RegisterTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_user_can_register_successfully(): void
    {
        $password = $this->faker->password(8, 12);

        $payload = [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => $password,
            'password_confirmation' => $password,
            'company_name' => $this->faker->company(),
            'company_domain' => $this->faker->domainName(),
            'role' => 'employee',
        ];
        $response = $this->postJson('/api/v1/auth/register', $payload);

        $response
            ->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'payload' => [
                    'user' => [
                        'id',
                        'name',
                        'email',
                        'role',
                        'company_id',
                    ],
                    'company' => [
                        'id',
                        'name',
                        'domain',
                    ],
                    'token',
                    'token_type',
                    'expires_in',
                ],
            ]);

        $this->assertDatabaseHas('users', [
            'email' => $payload['email'],
            'name' => $payload['name'],
            'role' => $payload['role'],
        ]);

        $this->assertDatabaseHas('companies', [
            'name' => $payload['company_name'],
            'domain' => $payload['company_domain'],
        ]);
    }

    public function test_register_fails_with_existing_email(): void
    {
        // Create a user first
        $existingUser = \App\Models\User::factory()->create();

        $password = $this->faker->password(8, 12);

        $payload = [
            'name' => $this->faker->name(),
            'email' => $existingUser->email, // Use existing email to cause failure
            'password' => $password,
            'password_confirmation' => $password,
            'company_name' => $this->faker->company(),
            'company_domain' => $this->faker->domainName(),
            'role' => 'employee',
        ];
        $response = $this->postJson('/api/v1/auth/register', $payload);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_register_fails_with_invalid_role(): void
    {
        $password = $this->faker->password(8, 12);

        $payload = [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => $password,
            'password_confirmation' => $password,
            'company_name' => $this->faker->company(),
            'company_domain' => $this->faker->domainName(),
            'role' => 'invalid_role', // Invalid role
        ];
        $response = $this->postJson('/api/v1/auth/register', $payload);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['role']);
    }

    public function test_register_fails_without_required_fields(): void
    {
        // Missing all required fields
        $payload = [];
        $response = $this->postJson('/api/v1/auth/register', $payload);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'password', 'company_name', 'company_domain', 'role']);
    }
    public function test_register_fails_with_password_mismatch(): void
    {
        $payload = [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => 'password123',
            'password_confirmation' => 'different_password', // Mismatch
            'company_name' => $this->faker->company(),
            'company_domain' => $this->faker->domainName(),
            'role' => 'employee',
        ];

        $response = $this->postJson('/api/v1/auth/register', $payload);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }
    public function test_register_fails_with_short_password(): void
    {
        $payload = [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => 'short', // Too short
            'password_confirmation' => 'short',
            'company_name' => $this->faker->company(),
            'company_domain' => $this->faker->domainName(),
            'role' => 'employee',
        ];

        $response = $this->postJson('/api/v1/auth/register', $payload);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }
    public function test_can_register_with_optional_fields(): void
    {
        $password = $this->faker->password(8, 12);

        $payload = [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => $password,
            'password_confirmation' => $password,
            'company_name' => $this->faker->company(),
            'company_domain' => $this->faker->domainName(),
            'role' => 'employee',
            'job_title' => $this->faker->jobTitle(),
            'department' => $this->faker->word(),
            'phone' => $this->faker->phoneNumber(),
        ];

        $response = $this->postJson('/api/v1/auth/register', $payload);

        $response->assertStatus(201);

        $this->assertDatabaseHas('users', [
            'email' => $payload['email'],
            'job_title' => $payload['job_title'],
            'department' => $payload['department'],
            'phone' => $payload['phone'],
        ]);
    }
    public function test_register_with_existing_company(): void
    {
        // Create a company first
        $company = Company::factory()->create();

        $password = $this->faker->password(8, 12);

        $payload = [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => $password,
            'password_confirmation' => $password,
            'company_name' => $this->faker->company(), // Company name doesn't matter if domain matches
            'company_domain' => $company->domain, // Use existing domain
            'role' => 'employee',
        ];

        $response = $this->postJson('/api/v1/auth/register', $payload);

        $response->assertStatus(201);

        // User should be associated with the existing company
        $userId = $response->json('payload.user.id');
        $this->assertDatabaseHas('users', [
            'id' => $userId,
            'company_id' => $company->id,
        ]);
    }
}
