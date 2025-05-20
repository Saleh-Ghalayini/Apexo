<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class LogoutTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_user_can_logout_successfully(): void
    {
        // Create a user
        $user = User::factory()->create([
            'active' => true,
        ]);

        // Generate token for user
        $token = JWTAuth::fromUser($user);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/v1/auth/logout');

        $response
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
                'payload' => [
                    'message' => 'Successfully logged out',
                ],
            ]);

        // Make a request with the same token to confirm it's invalidated
        $secondResponse = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/v1/user');

        $secondResponse->assertStatus(401);
    }
    public function test_logout_fails_without_token(): void
    {
        $response = $this->postJson('/api/v1/auth/logout');

        $response->assertStatus(401);
    }
    public function test_logout_fails_with_invalid_token(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer invalid_token',
        ])->postJson('/api/v1/auth/logout');

        $response->assertStatus(401);
    }

    public function test_cannot_use_token_after_logout(): void
    {
        // Create a user
        $user = User::factory()->create([
            'active' => true,
        ]);        // Generate token for user
        $token = JWTAuth::fromUser($user);

        // Logout
        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/v1/auth/logout');

        // Attempt to use the token again
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/v1/user');

        $response->assertStatus(401);
    }
}
