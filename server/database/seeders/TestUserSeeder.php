<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;

class TestUserSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::firstOrCreate([
            'domain' => 'apexotest.com',
        ], [
            'name' => 'Apexo Test Company',
        ]);
    }
}
