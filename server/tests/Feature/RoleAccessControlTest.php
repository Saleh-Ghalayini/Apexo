<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class RoleAccessControlTest extends TestCase
{
    /**
     * Current Test cases do not involve the AI integration and
     * are just the base of the Role Access Control.
     * The AI integration will be added in the future.
     */

    use DatabaseTransactions;

    private function createUserWithRole(string $role): User
    {
        return User::factory()->create(['role' => $role]);
    }

    public function testEmployeeAccessingManagerEndpoint()
    {
        $employee = $this->createUserWithRole('employee');

        $this->actingAs($employee);
        $response = $this->getJson('/api/v1/manager-only-endpoint');

        $response->assertStatus(403);
    }

    public function testEmployeeAccessingHREndpoint()
    {
        $employee = $this->createUserWithRole('employee');

        $this->actingAs($employee);
        $response = $this->getJson('/api/v1/hr-only-endpoint');

        $response->assertStatus(403);
    }

    public function testManagerAccessingHREndpoint()
    {
        $manager = $this->createUserWithRole('manager');

        $this->actingAs($manager);
        $response = $this->getJson('/api/v1/hr-only-endpoint');

        $response->assertStatus(403);
    }

    public function testManagerAccessingManagerEndpoint()
    {
        $manager = $this->createUserWithRole('manager');

        $this->actingAs($manager);
        $response = $this->getJson('/api/v1/manager-only-endpoint');

        $response->assertStatus(200);
    }

    public function testEmployeeAccessingEmployeeEndpoint()
    {
        $employee = $this->createUserWithRole('employee');

        $this->actingAs($employee);
        $response = $this->getJson('/api/v1/employee-only-endpoint');

        $response->assertStatus(200);
    }

    public function testHRAccessingHREndpoint()
    {
        $hr = $this->createUserWithRole('hr');

        $this->actingAs($hr);
        $response = $this->getJson('/api/v1/hr-only-endpoint');

        $response->assertStatus(200);
    }

    public function testHRAccessingManagerEndpoint()
    {
        $hr = $this->createUserWithRole('hr');

        $this->actingAs($hr);
        $response = $this->getJson('/api/v1/manager-only-endpoint');

        $response->assertStatus(403);
    }
}
