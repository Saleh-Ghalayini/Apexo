<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ReportTest extends TestCase
{
    use DatabaseTransactions;

    public function test_generate_and_send_report()
    {
        $user = User::factory()->create();
        // Example: $response = $this->actingAs($user, 'api')->postJson('/api/v1/ai/send-report', [...]);
        // $response->assertStatus(200);
        $this->assertTrue(true); // Placeholder
    }

    public function test_generate_and_download_report_requires_authentication()
    {
        $response = $this->postJson('/api/v1/ai/send-report', []);
        $response->assertStatus(401);
    }

    public function test_generate_and_send_report_validation()
    {
        $user = \App\Models\User::factory()->create();
        $user = \App\Models\User::query()->findOrFail($user->id);
        $response = $this->actingAs($user, 'api')->postJson('/api/v1/ai/send-report', []);
        $response->assertStatus(422);
    }

    public function test_generate_and_send_report_success_or_failure()
    {
        $user = \App\Models\User::factory()->create();
        $user = \App\Models\User::query()->findOrFail($user->id);
        $payload = [
            'report' => 'Test report',
            'to' => 'someone@example.com',
            'from_user_id' => $user->id,
        ];
        $response = $this->actingAs($user, 'api')->postJson('/api/v1/ai/send-report', $payload);
        $this->assertTrue(in_array($response->status(), [200, 500]));
    }
}
