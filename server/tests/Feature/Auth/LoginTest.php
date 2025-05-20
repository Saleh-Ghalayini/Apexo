<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Company;

class LoginTest extends TestCase
{
    use RefreshDatabase, WithFaker;
    public function test_user_can_login_successfully(): void
    {
        // Create a company
        $company = Company::factory()->create();

        // Create a user with known password
        $user = User::factory()->create([
            'password' => bcrypt('password123'),
            'company_id' => $company->id,
            'active' => true,
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $response
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'success',
                'payload' => [
                    'user',
                    'token',
                    'token_type',
                    'expires_in',
                ],
            ]);
    }
    public function test_login_fails_with_incorrect_credentials(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password123'),
            'active' => true,
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => $user->email,
            'password' => 'wrong_password',
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }
    public function test_login_fails_with_inactive_account(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password123'),
            'active' => false, // Inactive account
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }
    public function test_login_fails_with_nonexistent_email(): void
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'nonexistent@example.com',
            'password' => 'password123',
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }
    public function test_login_fails_without_required_fields(): void
    {
        // Missing all required fields
        $response = $this->postJson('/api/v1/auth/login', []);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'password']);
    }
    public function test_login_fails_with_invalid_email_format(): void
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'not-an-email',
            'password' => 'password123',
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }
}
