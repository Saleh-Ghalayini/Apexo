<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;

class ChatAITestDataSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::updateOrCreate(
            ['name' => 'Apexo Test Company'],
            [
                'domain' => 'apexo-test.example.com',
                'address' => '123 Test Street',
                'phone' => '123-456-7890',
                'website' => 'https://apexo-test.example.com',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
