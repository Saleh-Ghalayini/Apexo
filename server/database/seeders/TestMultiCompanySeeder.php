<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;

class TestMultiCompanySeeder extends Seeder
{
    public function run(): void
    {
        $company2 = Company::factory()->create([
            'name' => 'Second Test Company',
            'domain' => 'secondtest.com',
        ]);
    }
}
