<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AITest extends TestCase
{
    use DatabaseTransactions;

    public function test_ai_generate_task_report()
    {
        $user = User::factory()->create();
        $user = is_array($user) ? $user[0] : $user;
        $this->actingAs($user, 'api');
        $response = $this->getJson('/api/v1/ai/generate-task-report');
        $response->assertStatus(200)->assertJsonStructure(['payload' => ['report']]);
    }

    public function test_ai_meeting_analytics_requires_authentication()
    {
        $response = $this->postJson('/api/v1/ai/analyze-meeting/1', []);
        $response->assertStatus(401);
    }

    public function test_ai_employee_analytics_requires_authentication()
    {
        $response = $this->postJson('/api/v1/ai/analyze-employee/1', []);
        $response->assertStatus(401);
    }

    public function test_ai_generate_task_report_requires_authentication()
    {
        $response = $this->getJson('/api/v1/ai/generate-task-report');
        $response->assertStatus(401);
    }
}
