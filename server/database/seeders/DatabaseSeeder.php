<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $company = \App\Models\Company::firstOrCreate(
            [
                'domain' => 'apexodemo.com',
            ],
            [
                'name' => 'Apexo Demo Company',
            ]
        );
    }
}
