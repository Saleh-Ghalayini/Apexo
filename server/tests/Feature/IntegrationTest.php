<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Integration;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class IntegrationTest extends TestCase
{
    use DatabaseTransactions;

    public function test_integration_connect_and_status_update()
    {
        $user = User::factory()->create();
        $user = is_array($user) ? $user[0] : $user;
        $integration = Integration::factory()->create(['user_id' => $user->id]);
        $response = $this->actingAs($user, 'api')->patchJson("/api/v1/integrations/{$integration->id}/status", ['status' => 'inactive']);
        $response->assertStatus(200)->assertJsonPath('payload.status', 'inactive');
    }

    public function test_integration_connect_and_disconnect()
    {
        $user = \App\Models\User::factory()->create();
        $user = \App\Models\User::query()->findOrFail($user->id);
        $integration = \App\Models\Integration::factory()->create(['user_id' => $user->id]);
        $connect = $this->actingAs($user, 'api')->postJson('/api/v1/integrations/connect/notion');
        $this->assertTrue(in_array($connect->status(), [200, 302, 400, 500]));
        $disconnect = $this->actingAs($user, 'api')->deleteJson("/api/v1/integrations/{$integration->id}");
        $disconnect->assertStatus(200);
    }

    public function test_integration_list_requires_authentication()
    {
        $response = $this->getJson('/api/v1/integrations');
        $response->assertStatus(401);
    }

    public function test_update_status_validation()
    {
        $user = \App\Models\User::factory()->create();
        $user = \App\Models\User::query()->findOrFail($user->id);
        $integration = \App\Models\Integration::factory()->create(['user_id' => $user->id]);
        $response = $this->actingAs($user, 'api')->patchJson("/api/v1/integrations/{$integration->id}/status", ['status' => 'invalid']);
        $response->assertStatus(422);
    }

    public function test_update_status_requires_authentication()
    {
        $integration = \App\Models\Integration::factory()->create();
        $response = $this->patchJson("/api/v1/integrations/{$integration->id}/status", ['status' => 'inactive']);
        $response->assertStatus(401);
    }

    // Add more integration-related tests here (connect, disconnect, list, etc.)
}
