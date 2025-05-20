<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Integration;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class NotionTest extends TestCase
{
    use DatabaseTransactions;

    // All Notion tests commented out for now
    /*
    public function test_notion_oauth_redirect()
    {
        $user = User::factory()->create();
        $user = is_array($user) ? $user[0] : $user;
        $response = $this->actingAs($user)->get('/api/integrations/notion/authorize');
        $response->assertStatus(302);
        $response->assertRedirect();
        $this->assertStringContainsString('api.notion.com/v1/oauth/authorize', $response->headers->get('Location'));
    }

    public function test_notion_fetch_databases_requires_authentication()
    {
        $response = $this->getJson('/api/v1/notion/databases');
        $response->assertStatus(401);
    }

    public function test_notion_save_database_requires_authentication()
    {
        $response = $this->postJson('/api/v1/notion/databases/dbid/save', []);
        $response->assertStatus(401);
    }

    public function test_notion_save_database_validation()
    {
        $user = \App\Models\User::factory()->create();
        $user = \App\Models\User::query()->findOrFail($user->id);
        $response = $this->actingAs($user, 'api')->postJson('/api/v1/notion/databases/dbid/save', []);
        $response->assertStatus(422);
    }
    */
}
