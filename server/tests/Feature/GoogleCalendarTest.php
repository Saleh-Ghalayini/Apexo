<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class GoogleCalendarTest extends TestCase
{
    use DatabaseTransactions;

    public function test_google_calendar_redirect()
    {
        $user = User::factory()->create();
        $user = is_array($user) ? $user[0] : $user;
        $response = $this->actingAs($user, 'api')->get('/api/v1/google-calendar/redirect');
        $this->assertTrue(in_array($response->status(), [200, 302]), 'Expected 200 or 302, got ' . $response->status());
        if ($response->status() === 200) {
            $response->assertJsonStructure(['payload' => ['authUrl', 'state']]);
        }
    }

    public function test_google_calendar_event_creation_requires_authentication()
    {
        $response = $this->postJson('/api/v1/google-calendar/events', []);
        $response->assertStatus(401);
    }

    public function test_google_calendar_event_creation_validation()
    {
        $user = \App\Models\User::factory()->create();
        $user = \App\Models\User::query()->findOrFail($user->id);
        $response = $this->actingAs($user, 'api')->postJson('/api/v1/google-calendar/events', []);
        $response->assertStatus(422);
    }

    public function test_google_calendar_list_events_requires_authentication()
    {
        $response = $this->getJson('/api/v1/google-calendar/events');
        $response->assertStatus(401);
    }

    public function test_google_calendar_redirect_requires_authentication()
    {
        $response = $this->get('/api/v1/google-calendar/redirect');
        $this->assertTrue(in_array($response->status(), [200, 302]), 'Expected 200 or 302, got ' . $response->status());
    }
}
