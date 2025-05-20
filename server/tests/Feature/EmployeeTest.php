<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Company;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class EmployeeTest extends TestCase
{
    use DatabaseTransactions;

    public function test_hr_can_see_only_company_employees()
    {
        $company1 = Company::factory()->create();
        $company2 = Company::factory()->create();
        $hr = User::factory()->create(['company_id' => $company1->id, 'role' => 'hr']);
        $hr = is_array($hr) ? $hr[0] : $hr;
        $emp1 = User::factory()->create(['company_id' => $company1->id, 'role' => 'employee']);
        $emp2 = User::factory()->create(['company_id' => $company2->id, 'role' => 'employee']);
        $response = $this->actingAs($hr, 'api')->getJson('/api/v1/employees');
        $response->assertStatus(200);
        $ids = collect($response->json('payload'))->pluck('id');
        $this->assertTrue($ids->contains($emp1->id));
        $this->assertFalse($ids->contains($emp2->id));
    }

    public function test_hr_can_search_employees_by_name()
    {
        $company = \App\Models\Company::factory()->create();
        $hr = \App\Models\User::factory()->create(['company_id' => $company->id, 'role' => 'hr']);
        $hr = \App\Models\User::query()->findOrFail($hr->id);
        $emp1 = \App\Models\User::factory()->create(['company_id' => $company->id, 'role' => 'employee', 'name' => 'Alice']);
        $emp2 = \App\Models\User::factory()->create(['company_id' => $company->id, 'role' => 'employee', 'name' => 'Bob']);
        $response = $this->actingAs($hr, 'api')->getJson('/api/v1/employees/search?name=Alice');
        $response->assertStatus(200);
        $ids = collect($response->json('payload'))->pluck('id');
        $this->assertTrue($ids->contains($emp1->id));
        $this->assertFalse($ids->contains($emp2->id));
    }

    public function test_hr_get_employee_by_id()
    {
        $company = \App\Models\Company::factory()->create();
        $hr = \App\Models\User::factory()->create(['company_id' => $company->id, 'role' => 'hr']);
        $hr = \App\Models\User::query()->findOrFail($hr->id);
        $emp = \App\Models\User::factory()->create(['company_id' => $company->id, 'role' => 'employee']);
        $response = $this->actingAs($hr, 'api')->getJson("/api/v1/employees/{$emp->id}");
        $response->assertStatus(200)->assertJsonPath('payload.id', $emp->id);
    }

    public function test_hr_gets_404_for_employee_not_in_company()
    {
        $company1 = \App\Models\Company::factory()->create();
        $company2 = \App\Models\Company::factory()->create();
        $hr = \App\Models\User::factory()->create(['company_id' => $company1->id, 'role' => 'hr']);
        $hr = \App\Models\User::query()->findOrFail($hr->id);
        $emp = \App\Models\User::factory()->create(['company_id' => $company2->id, 'role' => 'employee']);
        $response = $this->actingAs($hr, 'api')->getJson("/api/v1/employees/{$emp->id}");
        $response->assertStatus(404);
    }

    public function test_hr_search_requires_name_param()
    {
        $company = \App\Models\Company::factory()->create();
        $hr = \App\Models\User::factory()->create(['company_id' => $company->id, 'role' => 'hr']);
        $hr = \App\Models\User::query()->findOrFail($hr->id);
        $response = $this->actingAs($hr, 'api')->getJson('/api/v1/employees/search');
        $response->assertStatus(422);
    }

    public function test_unauthenticated_cannot_access_hr_routes()
    {
        $response = $this->getJson('/api/v1/employees');
        $response->assertStatus(401);
    }

    public function test_hr_cannot_see_employees_from_other_company()
    {
        $company1 = \App\Models\Company::factory()->create();
        $company2 = \App\Models\Company::factory()->create();
        $hr = \App\Models\User::factory()->create(['company_id' => $company1->id, 'role' => 'hr']);
        $hr = \App\Models\User::query()->findOrFail($hr->id);
        $emp2 = \App\Models\User::factory()->create(['company_id' => $company2->id, 'role' => 'employee']);
        $response = $this->actingAs($hr, 'api')->getJson('/api/v1/employees');
        $ids = collect($response->json('payload'))->pluck('id');
        $this->assertFalse($ids->contains($emp2->id));
    }

    public function test_employee_cannot_access_hr_routes()
    {
        $company = \App\Models\Company::factory()->create();
        $employee = \App\Models\User::factory()->create(['company_id' => $company->id, 'role' => 'employee']);
        $employee = \App\Models\User::query()->findOrFail($employee->id);
        $response = $this->actingAs($employee, 'api')->getJson('/api/v1/employees');
        $response->assertStatus(403);
    }
}
