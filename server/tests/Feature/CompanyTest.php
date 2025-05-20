<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Company;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CompanyTest extends TestCase
{
    use DatabaseTransactions;

    public function test_company_creation()
    {
        $company = Company::factory()->create();
        $this->assertDatabaseHas('companies', ['id' => $company->id]);
    }

    public function test_company_update_and_fetch()
    {
        $company = Company::factory()->create(['name' => 'Original Name']);
        $company->update(['name' => 'Updated Name']);
        $this->assertDatabaseHas('companies', ['id' => $company->id, 'name' => 'Updated Name']);
        $fetched = Company::find($company->id);
        $this->assertEquals('Updated Name', $fetched->name);
    }

    public function test_company_creation_requires_name()
    {
        $company = Company::factory()->make(['name' => null]);
        $this->expectException(\Illuminate\Database\QueryException::class);
        $company->save();
    }

    public function test_company_duplicate_domain_fails()
    {
        $company = Company::factory()->create(['domain' => 'dupe.com']);
        $this->expectException(\Illuminate\Database\QueryException::class);
        Company::factory()->create(['domain' => 'dupe.com']);
    }

    // Add more company-related tests here
}
