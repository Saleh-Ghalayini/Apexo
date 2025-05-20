<?php

namespace Tests\Feature;

use App\Models\Integration;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class NotionIntegrationTest extends TestCase
{
    use DatabaseTransactions;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /**
     * Test that the Notion OAuth redirect endpoint works
     */
    // public function testNotionOAuthRedirect()
    // {
    //     $response = $this->actingAs($this->user)
    //         ->get('/api/integrations/notion/authorize');

    //     $response->assertStatus(302);
    //     $response->assertRedirect();
    //     $this->assertTrue(strpos($response->headers->get('Location'), 'api.notion.com/v1/oauth/authorize') !== false);
    // }

    /**
     * Test that we can fetch Notion databases with a valid integration
     */
    // public function testGetNotionDatabases()
    // {
    //     // Create a mock Notion integration
    //     $integration = Integration::factory()->create([
    //         'user_id' => $this->user->id,
    //         'provider' => 'notion',
    //         'status' => 'active',
    //         'data' => [
    //             'access_token' => 'mock_access_token',
    //             'workspace_id' => 'mock_workspace_id',
    //             'workspace_name' => 'Mock Workspace',
    //             'workspace_icon' => null,
    //             'token_type' => 'bearer',
    //             'bot_id' => 'mock_bot_id'
    //         ]
    //     ]);

    //     // Mock HTTP response for databases
    //     $this->mock(\Illuminate\Http\Client\Factory::class, function ($mock) {
    //         $mock->shouldReceive('withToken->get->json')
    //             ->andReturn([
    //                 'results' => [
    //                     [
    //                         'id' => 'db_123',
    //                         'title' => [['plain_text' => 'Test Database']],
    //                         'properties' => [
    //                             'Name' => ['id' => 'title', 'type' => 'title'],
    //                             'Status' => ['id' => 'status', 'type' => 'select']
    //                         ]
    //                     ]
    //                 ]
    //             ]);
    //     });

    //     $response = $this->actingAs($this->user)
    //         ->getJson("/api/integrations/notion/databases");

    //     $response->assertStatus(200)
    //         ->assertJsonStructure([
    //             'data' => [
    //                 '*' => [
    //                     'id',
    //                     'title',
    //                     'properties'
    //                 ]
    //             ]
    //         ]);
    // }

    /**
     * Test that we can save a Notion database
     */
    // public function testSaveNotionDatabase()
    // {
    //     // Create a mock Notion integration
    //     $integration = Integration::factory()->create([
    //         'user_id' => $this->user->id,
    //         'provider' => 'notion',
    //         'status' => 'active',
    //         'data' => [
    //             'access_token' => 'mock_access_token',
    //             'workspace_id' => 'mock_workspace_id',
    //             'workspace_name' => 'Mock Workspace',
    //             'workspace_icon' => null,
    //             'token_type' => 'bearer',
    //             'bot_id' => 'mock_bot_id'
    //         ]
    //     ]);

    //     // Mock HTTP response for getting database
    //     $this->mock(\Illuminate\Http\Client\Factory::class, function ($mock) {
    //         $mock->shouldReceive('withToken->get->json')
    //             ->andReturn([
    //                 'id' => 'db_123',
    //                 'title' => [['plain_text' => 'Test Database']],
    //                 'properties' => [
    //                     'Name' => ['id' => 'title', 'type' => 'title'],
    //                     'Status' => ['id' => 'status', 'type' => 'select']
    //                 ]
    //             ]);
    //     });

    //     $response = $this->actingAs($this->user)
    //         ->postJson("/api/integrations/notion/databases/db_123/save");

    //     $response->assertStatus(200)
    //         ->assertJsonStructure([
    //             'data' => [
    //                 'id',
    //                 'database_id',
    //                 'title',
    //                 'properties'
    //             ]
    //         ]);
    // }

    public function test_notion_integration_connect_and_disconnect()
    {
        $user = \App\Models\User::factory()->create();
        $user = \App\Models\User::query()->findOrFail($user->id);
        $integration = \App\Models\Integration::factory()->create([
            'user_id' => $user->id,
            'provider' => 'notion',
            'status' => 'active',
        ]);
        $disconnect = $this->actingAs($user, 'api')->deleteJson("/api/v1/integrations/{$integration->id}");
        $disconnect->assertStatus(200);
    }

    public function test_notion_integration_list_requires_authentication()
    {
        $response = $this->getJson('/api/v1/integrations');
        $response->assertStatus(401);
    }
}
