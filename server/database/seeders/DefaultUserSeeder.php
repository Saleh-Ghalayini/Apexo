<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DefaultUserSeeder extends Seeder
{
    public function run(): void
    {
        $company = \App\Models\Company::factory()->create([
            'name' => 'Apexo Demo Company',
            'domain' => 'apexo.local',
            'active' => true,
        ]);

        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'hr',
            'company_id' => $company->id,
        ]);
    }
}
