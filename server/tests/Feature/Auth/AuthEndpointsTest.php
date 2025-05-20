<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use Illuminate\Support\Facades\Config;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthEndpointsTest extends TestCase
{
    use RefreshDatabase;

    public function test_auth_endpoints_exist(): void
    {
        $response = $this->postJson('/api/v1/auth/register', []);
        $this->assertTrue(
            $response->status() == 422,
            'Register endpoint should exist and return 422 validation error, but got ' . $response->status()
        );

        $response = $this->postJson('/api/v1/auth/login', []);
        $this->assertTrue(
            $response->status() == 422,
            'Login endpoint should exist and return 422 validation error, but got ' . $response->status()
        );

        $response = $this->postJson('/api/v1/auth/logout');
        $this->assertTrue(
            $response->status() == 401,
            'Logout endpoint should exist and return 401 unauthorized, but got ' . $response->status()
        );

        $response = $this->postJson('/api/v1/auth/refresh');
        $this->assertTrue(
            $response->status() == 401,
            'Refresh endpoint should exist and return 401 unauthorized, but got ' . $response->status()
        );
    }

    public function test_jwt_config_is_properly_set(): void
    {
        $secret = Config::get('jwt.secret');
        $this->assertNotEmpty($secret, 'JWT secret should be set');

        $ttl = Config::get('jwt.ttl');
        $this->assertNotEmpty($ttl, 'JWT TTL should be set');
    }
}
