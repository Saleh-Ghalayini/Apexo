<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Task;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use DatabaseTransactions;

    public function test_task_creation_with_factory()
    {
        $user = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $user->id]);
        $this->assertDatabaseHas('tasks', ['id' => $task->id, 'user_id' => $user->id]);
    }

    public function test_task_update_and_delete()
    {
        $user = \App\Models\User::factory()->create();
        $user = \App\Models\User::query()->findOrFail($user->id);
        $task = \App\Models\Task::factory()->create(['user_id' => $user->id]);
        $update = $task->update(['title' => 'Updated Task']);
        $this->assertTrue($update);
        $this->assertDatabaseHas('tasks', ['id' => $task->id, 'title' => 'Updated Task']);
        $task->delete();
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    public function test_task_creation_requires_user_id()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);
        \App\Models\Task::factory()->create(['user_id' => null]);
    }

    public function test_task_forbidden_for_unauthenticated()
    {
        $response = $this->postJson('/api/v1/tasks', []);
        $this->assertEquals(401, $response->status(), 'Expected 401 for unauthenticated, got ' . $response->status() . ': ' . $response->getContent());
    }
}
