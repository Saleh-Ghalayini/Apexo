<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Company;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

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

        $employee = User::updateOrCreate(
            ['email' => 'employee@example.com'],
            [
                'company_id' => $company->id,
                'name' => 'Regular Employee',
                'password' => Hash::make('password'),
                'role' => 'employee',
                'job_title' => 'Software Developer',
                'department' => 'Engineering',
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        $manager = User::updateOrCreate(
            ['email' => 'manager@example.com'],
            [
                'company_id' => $company->id,
                'name' => 'Department Manager',
                'password' => Hash::make('password'),
                'role' => 'manager',
                'job_title' => 'Engineering Manager',
                'department' => 'Engineering',
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
