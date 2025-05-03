<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Company;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class RegistrationTest extends TestCase
{
    use DatabaseTransactions;

    public function testRegisterWithValidData()
    {
        $password = "password123";

        $company_name = fake()->company();
        $company_domain = 'https://www.google.com';

        $user_data = [
            'company_name' => $company_name,
            'company_domain' => $company_domain,
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => $password,
            'password_confirmation' => $password,
            'role' => fake()->randomElement(['employee', 'manager', 'hr']),
        ];

        $response = $this->postJson('/api/v1/auth/register', $user_data);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'payload' => [
                    'user' => [
                        'id',
                        'name',
                        'email',
                    ]
                ]
            ])
            ->assertJson([
                'success' => true,
                'payload' => [
                    'user' => [
                        'name' => $user_data['name'],
                        'email' => $user_data['email'],
                    ]
                ]
            ]);

        $company = Company::where('domain', $company_domain)->first();

        $this->assertDatabaseHas('users', [
            'email' => $user_data['email'],
            'name' => $user_data['name'],
            'company_id' => $company->id,
            'role' => $user_data['role'],
        ]);
    }

    public function testRegisterWithDuplicateEmail()
    {
        $existing_user = User::factory()->create();

        $password = "password123";

        $user_data = [
            'company_name' => $existing_user->company->name,
            'company_domain' => $existing_user->company->domain,
            'name' => fake()->name(),
            'email' => $existing_user->email,
            'password' => $password,
            'password_confirmation' => $password,
        ];

        $response = $this->postJson('/api/v1/auth/register', $user_data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function testRegisterWithMissingFields()
    {
        $response = $this->postJson('/api/v1/auth/register', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(
                [
                    'name',
                    'email',
                    'password',
                    'company_name',
                    'company_domain'
                ]
            );
    }

    public function testRegisterWithPasswordMismatch()
    {
        $company_name = fake()->company();
        $company_domain = 'https://www.google.com';

        $user_data = [
            'company_name' => $company_name,
            'company_domain' => $company_domain,
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => 'password123',
            'password_confirmation' => 'differentPassword',
            'role' => fake()->randomElement(['employee', 'manager', 'hr']),
        ];

        $response = $this->postJson('/api/v1/auth/register', $user_data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password_confirmation']);
    }

    public function testRegisterWithInvalidEmail()
    {
        $company_name = fake()->company();
        $company_domain = 'https://www.google.com';

        $user_data = [
            'company_name' => $company_name,
            'company_domain' => $company_domain,
            'name' => fake()->name(),
            'email' => 'not-an-email',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => fake()->randomElement(['employee', 'manager', 'hr']),
        ];

        $response = $this->postJson('/api/v1/auth/register', $user_data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }
}
