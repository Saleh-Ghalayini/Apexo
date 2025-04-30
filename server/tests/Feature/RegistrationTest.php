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
        $company = Company::factory()->create();

        $user_data = [
            'company_id' => $company->id,
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => $password,
            'password_confirmation' => $password,
        ];

        $response = $this->postJson('/api/v1/auth/register', $user_data);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'payload' => [
                    'token',
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

        $this->assertDatabaseHas('users', [
            'email' => $user_data['email'],
            'name' => $user_data['name'],
            'company_id' => $company->id,
        ]);
    }

    public function testRegisterWithDuplicateEmail()
    {
        $existing_user = User::factory()->create();

        $password = "password123";

        $user_data = [
            'company_id' => $existing_user->company_id ?? Company::factory()->create()->id,
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
            ->assertJsonValidationErrors(['name', 'email', 'password', 'company_id']);
    }

    public function testRegisterWithPasswordMismatch()
    {
        $company = Company::factory()->create();

        $user_data = [
            'company_id' => $company->id,
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => 'password123',
            'password_confirmation' => 'differentPassword',
        ];

        $response = $this->postJson('/api/v1/auth/register', $user_data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    public function testRegisterWithInvalidEmail()
    {
        $company = Company::factory()->create();

        $user_data = [
            'company_id' => $company->id,
            'name' => fake()->name(),
            'email' => 'not-an-email',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->postJson('/api/v1/auth/register', $user_data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }
}
