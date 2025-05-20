<?php

namespace Tests\Unit;

use App\Models\User;
use App\Services\HrService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Mockery;
use Tests\TestCase;

class HrServiceTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        // Prevent actual Auth facade calls
        Auth::shouldReceive('user')->andReturnUsing(function () {
            return User::factory()->create(['company_id' => 123]);
        });
    }

    public function test_get_all_employees_returns_only_company_users()
    {
        $user = Auth::user();
        User::factory()->count(2)->create(['company_id' => $user->company_id]);
        User::factory()->count(2)->create(['company_id' => 999]);
        $service = new HrService();
        $employees = $service->getAllEmployees();
        $this->assertCount(3, $employees); // 2 + the Auth user
        $this->assertTrue($employees->every(fn($u) => $u->company_id === $user->company_id));
    }

    public function test_search_employees_by_name_filters_by_company_and_name()
    {
        $user = Auth::user();
        User::factory()->create(['company_id' => $user->company_id, 'name' => 'Alice Smith']);
        User::factory()->create(['company_id' => $user->company_id, 'name' => 'Bob Jones']);
        User::factory()->create(['company_id' => 999, 'name' => 'Alice Smith']);
        $service = new HrService();
        $results = $service->searchEmployeesByName('Alice');
        $this->assertCount(1, $results);
        $this->assertEquals('Alice Smith', $results->first()->name);
    }

    public function test_get_employee_by_id_returns_null_for_other_company()
    {
        $user = Auth::user();
        $other = User::factory()->create(['company_id' => 999]);
        $service = new HrService();
        $result = $service->getEmployeeById($other->id);
        $this->assertNull($result);
    }

    public function test_get_employee_by_id_returns_user_for_same_company()
    {
        $user = Auth::user();
        $other = User::factory()->create(['company_id' => $user->company_id]);
        $service = new HrService();
        $result = $service->getEmployeeById($other->id);
        $this->assertNotNull($result);
        $this->assertEquals($other->id, $result->id);
    }
}
