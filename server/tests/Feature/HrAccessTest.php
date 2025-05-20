<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;

class HrAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_hr_user_can_access_company_employees()
    {
        $company1 = Company::factory()->create();
        $company2 = Company::factory()->create();

        $hrUser = User::factory()->create([
            'company_id' => $company1->id,
            'role' => 'hr',
        ]);
        $employee1 = User::factory()->create([
            'company_id' => $company1->id,
            'role' => 'employee',
        ]);
        $employee2 = User::factory()->create([
            'company_id' => $company2->id,
            'role' => 'employee',
        ]);

        $hrUser = User::query()->findOrFail($hrUser->id);
        $response = $this->actingAs($hrUser, 'api')
            ->getJson('/api/v1/employees');

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data')
            ->assertJsonFragment(['id' => $employee1->id])
            ->assertJsonMissing(['id' => $employee2->id]);
    }

    public function test_hr_user_can_search_employees_by_name()
    {
        $company = Company::factory()->create();
        $hrUser = User::factory()->create([
            'company_id' => $company->id,
            'role' => 'hr',
        ]);
        $employee1 = User::factory()->create([
            'company_id' => $company->id,
            'name' => 'John Smith',
            'role' => 'employee',
        ]);
        $employee2 = User::factory()->create([
            'company_id' => $company->id,
            'name' => 'Jane Doe',
            'role' => 'employee',
        ]);

        $hrUser = User::query()->findOrFail($hrUser->id);
        $response = $this->actingAs($hrUser, 'api')
            ->getJson('/api/v1/employees/search?name=John');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment(['id' => $employee1->id])
            ->assertJsonMissing(['id' => $employee2->id]);
    }

    public function test_non_hr_user_cannot_access_employee_data()
    {
        $company = Company::factory()->create();
        $employee = User::factory()->create([
            'company_id' => $company->id,
            'role' => 'employee',
        ]);

        $employee = User::query()->findOrFail($employee->id);
        $response = $this->actingAs($employee, 'api')
            ->getJson('/api/v1/employees');

        $response->assertStatus(403);
    }

    public function test_hr_user_can_view_single_employee_in_own_company()
    {
        $company = Company::factory()->create();
        $hrUser = User::factory()->create([
            'company_id' => $company->id,
            'role' => 'hr',
        ]);
        $employee = User::factory()->create([
            'company_id' => $company->id,
            'role' => 'employee',
        ]);

        $hrUser = User::query()->findOrFail($hrUser->id);
        $response = $this->actingAs($hrUser, 'api')
            ->getJson("/api/v1/employees/{$employee->id}");

        $response->assertStatus(200)
            ->assertJsonFragment(['id' => $employee->id]);
    }

    public function test_hr_user_cannot_view_employee_in_other_company()
    {
        $company1 = Company::factory()->create();
        $company2 = Company::factory()->create();
        $hrUser = User::factory()->create([
            'company_id' => $company1->id,
            'role' => 'hr',
        ]);
        $employee = User::factory()->create([
            'company_id' => $company2->id,
            'role' => 'employee',
        ]);

        $hrUser = User::query()->findOrFail($hrUser->id);
        $response = $this->actingAs($hrUser, 'api')
            ->getJson("/api/v1/employees/{$employee->id}");

        $response->assertStatus(404);
    }

    public function test_hr_user_gets_404_for_nonexistent_employee()
    {
        $company = Company::factory()->create();
        $hrUser = User::factory()->create([
            'company_id' => $company->id,
            'role' => 'hr',
        ]);

        $hrUser = User::query()->findOrFail($hrUser->id);
        $response = $this->actingAs($hrUser, 'api')
            ->getJson('/api/v1/employees/999999');

        $response->assertStatus(404);
    }

    public function test_search_employees_requires_name_param()
    {
        $company = Company::factory()->create();
        $hrUser = User::factory()->create([
            'company_id' => $company->id,
            'role' => 'hr',
        ]);

        $hrUser = User::query()->findOrFail($hrUser->id);
        $response = $this->actingAs($hrUser, 'api')
            ->getJson('/api/v1/employees/search');

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    public function test_search_employees_requires_min_length()
    {
        $company = Company::factory()->create();
        $hrUser = User::factory()->create([
            'company_id' => $company->id,
            'role' => 'hr',
        ]);

        $hrUser = User::query()->findOrFail($hrUser->id);
        $response = $this->actingAs($hrUser, 'api')
            ->getJson('/api/v1/employees/search?name=A');

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    public function test_manager_cannot_access_hr_routes()
    {
        $company = Company::factory()->create();
        $manager = User::factory()->create([
            'company_id' => $company->id,
            'role' => 'manager',
        ]);

        $manager = User::query()->findOrFail($manager->id);
        $response = $this->actingAs($manager, 'api')
            ->getJson('/api/v1/employees');

        $response->assertStatus(403);
    }

    public function test_unauthenticated_user_cannot_access_hr_routes()
    {
        $response = $this->getJson('/api/v1/employees');
        $response->assertStatus(401);
    }
}
