<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Meeting;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class MeetingTest extends TestCase
{
    use DatabaseTransactions;

    public function test_meeting_creation_and_fetch()
    {
        $user = User::factory()->create();
        $user = is_array($user) ? $user[0] : $user;
        $payload = [
            'user_id' => $user->id,
            'title' => 'Test Meeting',
            'scheduled_at' => now()->addDay()->toDateTimeString(),
        ];
        $response = $this->actingAs($user, 'api')->postJson('/api/v1/meetings', $payload);
        $response->assertStatus(201);
        $meetingId = $response->json('payload.id');
        $fetch = $this->actingAs($user, 'api')->getJson("/api/v1/meetings/{$meetingId}");
        $fetch->assertStatus(200)->assertJsonPath('payload.id', $meetingId);
    }

    public function test_meeting_update()
    {
        $user = User::factory()->create();
        $user = is_array($user) ? $user[0] : $user;
        $meeting = Meeting::factory()->create(['user_id' => $user->id]);
        $response = $this->actingAs($user, 'api')->patchJson("/api/v1/meetings/{$meeting->id}", ['title' => 'Updated Title']);
        $response->assertStatus(200)->assertJsonPath('payload.title', 'Updated Title');
    }

    public function test_meeting_creation_requires_authentication()
    {
        $response = $this->postJson('/api/v1/meetings', []);
        $response->assertStatus(401);
    }

    public function test_meeting_creation_validation()
    {
        $user = \App\Models\User::factory()->create();
        $user = \App\Models\User::query()->findOrFail($user->id);
        $response = $this->actingAs($user, 'api')->postJson('/api/v1/meetings', []);
        $response->assertStatus(422);
    }

    public function test_meeting_update_forbidden_for_other_user()
    {
        $user1 = \App\Models\User::factory()->create();
        $user2 = \App\Models\User::factory()->create();
        $user1 = \App\Models\User::query()->findOrFail($user1->id);
        $user2 = \App\Models\User::query()->findOrFail($user2->id);
        $meeting = \App\Models\Meeting::factory()->create(['user_id' => $user1->id]);
        $response = $this->actingAs($user2, 'api')->patchJson("/api/v1/meetings/{$meeting->id}", ['title' => 'Hacked']);
        $this->assertTrue(in_array($response->status(), [403, 404]));
    }
}
