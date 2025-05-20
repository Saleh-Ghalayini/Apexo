<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class TokenRefreshTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_user_can_refresh_token_successfully(): void
    {
        // Create a user
        $user = User::factory()->create([
            'active' => true,
        ]);
        // Generate token for user
        $token = JWTAuth::fromUser($user);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/v1/auth/refresh');

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
            ]);        // Check that the new token is different from the old one
        $newToken = $response->json('payload.token');
        $this->assertNotEquals($token, $newToken);
    }
    public function test_refresh_fails_without_token(): void
    {
        $response = $this->postJson('/api/v1/auth/refresh');
        $response->assertStatus(401);
    }

    public function test_refresh_fails_with_invalid_token(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer invalid_token',
        ])->postJson('/api/v1/auth/refresh');

        $response->assertStatus(401);
    }

    public function test_refresh_fails_for_inactive_user(): void
    {
        // Create an inactive user
        $user = User::factory()->create([
            'active' => false,
        ]);
        // Generate token for inactive user
        $token = JWTAuth::fromUser($user);
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/v1/auth/refresh');
        $response->assertStatus(401);
    }
    public function test_new_token_is_different_after_refresh(): void
    {
        // Create a user
        $user = User::factory()->create([
            'active' => true,
        ]);

        // Generate token for user
        $token = JWTAuth::fromUser($user);

        // Refresh the token
        $refreshResponse = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/v1/auth/refresh');

        $refreshResponse->assertStatus(200);

        // Get the new token from the response
        $newToken = $refreshResponse->json('payload.token');

        // Make sure the new token is different from the old one
        $this->assertNotEquals($token, $newToken);

        // Verify the new token works
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $newToken,
        ])->getJson('/api/v1/user');

        $response->assertStatus(200);
    }
}
