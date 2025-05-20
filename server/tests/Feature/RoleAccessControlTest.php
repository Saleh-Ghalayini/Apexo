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

    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    // private function createUserWithRole(string $role): User
    // {
    //     return User::factory()->create(['role' => strtolower($role)]);
    // }

    // private function assertForbidden($response)
    // {
    //     $response->assertStatus(403)
    //         ->assertJson([
    //             'success' => false,
    //             'error' => 'Unauthorized access',
    //         ]);
    // }

    // private function assertSuccess($response)
    // {
    //     $response->assertStatus(200)
    //         ->assertJsonStructure([
    //             'success',
    //             'payload',
    //         ])
    //         ->assertJson([
    //             'success' => true,
    //         ]);
    // }

    // public function testEmployeeAccessingManagerEndpoint()
    // {
    //     $employee = $this->createUserWithRole('employee');
    //     $this->actingAs($employee);

    //     $response = $this->getJson('/api/v1/manager-only-endpoint');

    //     $this->assertForbidden($response);
    // }

    // public function testEmployeeAccessingHREndpoint()
    // {
    //     $employee = $this->createUserWithRole('employee');
    //     $this->actingAs($employee);

    //     $response = $this->getJson('/api/v1/hr-only-endpoint');

    //     $this->assertForbidden($response);
    // }

    // public function testManagerAccessingHREndpoint()
    // {
    //     $manager = $this->createUserWithRole('manager');
    //     $this->actingAs($manager);

    //     $response = $this->getJson('/api/v1/hr-only-endpoint');

    //     $this->assertForbidden($response);
    // }

    // public function testManagerAccessingManagerEndpoint()
    // {
    //     $manager = $this->createUserWithRole('manager');
    //     $this->actingAs($manager);

    //     $response = $this->getJson('/api/v1/manager-only-endpoint');

    //     $this->assertSuccess($response);
    // }

    // public function testEmployeeAccessingEmployeeEndpoint()
    // {
    //     $employee = $this->createUserWithRole('employee');
    //     $this->actingAs($employee);

    //     $response = $this->getJson('/api/v1/employee-only-endpoint');

    //     $this->assertSuccess($response);
    // }

    // public function testHRAccessingHREndpoint()
    // {
    //     $hr = $this->createUserWithRole('hr');
    //     $this->actingAs($hr);

    //     $response = $this->getJson('/api/v1/hr-only-endpoint');

    //     $this->assertSuccess($response);
    // }

    // public function testHRAccessingManagerEndpoint()
    // {
    //     $hr = $this->createUserWithRole('hr');
    //     $this->actingAs($hr);

    //     $response = $this->getJson('/api/v1/manager-only-endpoint');

    //     $this->assertForbidden($response);
    // }
}
