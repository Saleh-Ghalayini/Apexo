<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\IntegrationCredential;
use App\Services\AnnouncementService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AnnouncementServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_send_to_slack_returns_error_if_no_credential()
    {
        $user = User::factory()->create();
        $service = new AnnouncementService();
        $result = $service->sendToSlack($user, ['message' => 'Test', 'slack_channel' => 'general', 'title' => 'Test']);
        $this->assertFalse($result['success']);
        $this->assertEquals('Slack not connected', $result['error']);
    }

    public function test_send_to_slack_returns_error_on_api_failure()
    {
        $user = User::factory()->create();
        IntegrationCredential::factory()->create([
            'user_id' => $user->id,
            'type' => 'slack',
            'access_token' => 'fake-token',
        ]);
        Http::fake([
            'slack.com/api/chat.postMessage' => Http::response(['ok' => false], 200),
        ]);
        $service = new AnnouncementService();
        $result = $service->sendToSlack($user, ['message' => 'Test', 'slack_channel' => 'general', 'title' => 'Test']);
        $this->assertFalse($result['success']);
        $this->assertEquals('Slack API error', $result['error']);
    }
}
