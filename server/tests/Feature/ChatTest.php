<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ChatTest extends TestCase
{
    use DatabaseTransactions;

    public function test_create_and_fetch_chat_session()
    {
        $user = User::factory()->create();
        $user = is_array($user) ? $user[0] : $user;
        $payload = [
            'title' => 'Test Chat',
            'initial_message' => 'Hello!'
        ];
        $response = $this->actingAs($user, 'api')->postJson('/api/v1/chat/sessions', $payload);
        $response->assertStatus(201);
        $sessionId = $response->json('payload.session.id');
        $fetch = $this->actingAs($user, 'api')->getJson("/api/v1/chat/sessions/{$sessionId}");
        $fetch->assertStatus(200)->assertJsonPath('payload.id', $sessionId);
    }

    public function test_send_and_fetch_chat_message()
    {
        $user = User::factory()->create();
        $user = is_array($user) ? $user[0] : $user;
        $payload = [
            'title' => 'Test Chat',
            'initial_message' => 'Hello!'
        ];
        $session = $this->actingAs($user, 'api')->postJson('/api/v1/chat/sessions', $payload)->json('payload.session');
        $messagePayload = ['message' => 'Second message'];
        $send = $this->actingAs($user, 'api')->postJson("/api/v1/chat/sessions/{$session['id']}/messages", $messagePayload);
        $send->assertStatus(200);
        $fetch = $this->actingAs($user, 'api')->getJson("/api/v1/chat/sessions/{$session['id']}");
        $fetch->assertStatus(200)->assertJsonPath('payload.id', $session['id']);
        $this->assertTrue(collect($fetch->json('payload.messages'))->contains('content', 'Second message'));
    }

    public function test_archive_and_delete_chat_session()
    {
        $user = User::factory()->create();
        $user = is_array($user) ? $user[0] : $user;
        $session = $this->actingAs($user, 'api')->postJson('/api/v1/chat/sessions', [
            'title' => 'To Archive',
            'initial_message' => 'Hi',
        ])->json('payload.session');
        $archive = $this->actingAs($user, 'api')->postJson("/api/v1/chat/sessions/{$session['id']}/archive");
        $archive->assertStatus(200);
        $delete = $this->actingAs($user, 'api')->deleteJson("/api/v1/chat/sessions/{$session['id']}");
        $delete->assertStatus(200);
    }

    public function test_forbidden_chat_access_for_unauthenticated()
    {
        $response = $this->getJson('/api/v1/chat/sessions');
        $response->assertStatus(401);
    }
}
