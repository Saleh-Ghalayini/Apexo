<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class JwtAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register(): void
    {
        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'company_name' => 'Test Company',
            'company_domain' => 'testcompany.com',
            'role' => 'employee',
        ];

        $response = $this->postJson('/api/v1/auth/register', $userData);
        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'payload' => [
                    'user',
                    'company',
                    'token',
                    'token_type',
                    'expires_in',
                ],
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'name' => 'Test User',
        ]);

        $this->assertDatabaseHas('companies', [
            'name' => 'Test Company',
            'domain' => 'testcompany.com',
        ]);
    }

    public function test_user_can_login(): void
    {
        $user = User::factory()->create([
            'email' => 'login@example.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'login@example.com',
            'password' => 'password',
        ]);
        $response->assertStatus(200)
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

    public function test_user_can_logout(): void
    {
        $user = User::factory()->create();
        $token = auth('api')->login($user);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/v1/auth/logout');
        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'payload' => [
                    'message' => 'Successfully logged out'
                ]
            ]);

        $checkResponse = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/v1/user');

        $checkResponse->assertStatus(401);
    }

    public function test_user_can_refresh_token(): void
    {
        $user = User::factory()->create();
        $token = auth('api')->login($user);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/v1/auth/refresh');
        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'payload' => [
                    'user',
                    'token',
                    'token_type',
                    'expires_in',
                ],
            ]);
        $this->assertNotEquals($token, $response->json('payload.token'));
    }
}
