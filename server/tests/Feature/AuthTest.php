<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AuthTest extends TestCase
{
    use DatabaseTransactions;

    public function test_register_and_login()
    {
        $userData = [
            'name' => 'Test User',
            'email' => 'testuser@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'company_name' => 'Test Company',
            'company_domain' => 'testcompany.com',
            'role' => 'manager', // must be one of: employee, manager, hr
        ];
        $register = $this->postJson('/api/v1/auth/register', $userData);
        $register->assertStatus(201);
        $login = $this->postJson('/api/v1/auth/login', [
            'email' => $userData['email'],
            'password' => $userData['password'],
        ]);
        $login->assertStatus(200)->assertJsonStructure(['payload' => ['token']]);
    }

    public function test_register_requires_all_fields()
    {
        $response = $this->postJson('/api/v1/auth/register', []);
        $response->assertStatus(422)->assertJsonValidationErrors([
            'name',
            'email',
            'password',
            'company_name',
            'company_domain',
            'role',
        ]);
    }

    public function test_register_fails_with_duplicate_email()
    {
        $user = User::factory()->create(['email' => 'dupe@example.com']);
        $userData = [
            'name' => 'Test User',
            'email' => 'dupe@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'company_name' => 'Test Company',
            'company_domain' => 'dupecompany.com',
            'role' => 'manager', // must be one of: employee, manager, hr
        ];
        $response = $this->postJson('/api/v1/auth/register', $userData);
        $response->assertStatus(422)->assertJsonValidationErrors(['email']);
    }

    public function test_login_requires_email_and_password()
    {
        $response = $this->postJson('/api/v1/auth/login', []);
        $response->assertStatus(422)->assertJsonValidationErrors(['email', 'password']);
    }

    public function test_login_fails_with_wrong_password()
    {
        $user = User::factory()->create(['email' => 'wrongpass@example.com']);
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'wrongpass@example.com',
            'password' => 'incorrect',
        ]);
        $response->assertStatus(422)->assertJsonPath('errors.email.0', 'The provided credentials are incorrect.');
    }

    public function test_inactive_user_cannot_login()
    {
        $user = User::factory()->create(['email' => 'inactive@example.com', 'active' => false]);
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'inactive@example.com',
            'password' => 'password',
        ]);
        $response->assertStatus(422)->assertJsonPath('errors.email.0', 'This account has been deactivated.');
    }

    public function test_logout_and_refresh_token()
    {
        $user = User::factory()->create();
        $login = $this->postJson('/api/v1/auth/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);
        $token = $login->json('payload.access_token') ?? $login->json('payload.token');
        $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/v1/auth/logout')
            ->assertStatus(200);
        $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/v1/auth/refresh')
            ->assertStatus(401);
    }

    public function test_refresh_token_requires_authentication()
    {
        $this->postJson('/api/v1/auth/refresh')->assertStatus(401);
    }
}
