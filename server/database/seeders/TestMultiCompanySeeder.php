<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Company;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestMultiCompanySeeder extends Seeder
{
    public function run(): void
    {
        $company2 = Company::factory()->create([
            'name' => 'Second Test Company',
            'domain' => 'secondtest.com',
        ]);

        $hr2 = User::factory()->create([
            'name' => 'HR User Company 2',
            'email' => 'hr@secondtest.com',
            'password' => Hash::make('password'),
            'role' => 'hr',
            'company_id' => $company2->id,
        ]);
    }
}
