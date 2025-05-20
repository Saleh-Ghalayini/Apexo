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

        $admin = \App\Models\User::firstOrCreate(
            [
                'email' => 'admin@apexodemo.com',
            ],
            [
                'name' => 'Admin User',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'role' => 'hr',
                'company_id' => $company->id,
            ]
        );

        $manager = \App\Models\User::firstOrCreate(
            [
                'email' => 'manager@apexodemo.com',
            ],
            [
                'name' => 'Manager User',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'role' => 'manager',
                'company_id' => $company->id,
            ]
        );

        $employee = \App\Models\User::firstOrCreate(
            [
                'email' => 'employee@apexodemo.com',
            ],
            [
                'name' => 'Employee User',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'role' => 'employee',
                'company_id' => $company->id,
            ]
        );
    }
}
