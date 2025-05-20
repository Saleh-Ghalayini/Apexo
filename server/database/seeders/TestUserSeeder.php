<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Company;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestUserSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::firstOrCreate([
            'domain' => 'apexotest.com',
        ], [
            'name' => 'Apexo Test Company',
        ]);

        $testUsers = [
            [
                'name' => 'Saleh Ghalayini',
                'email' => 'ghalayinisaleh4@gmail.com',
            ],
            [
                'name' => 'Saleh Ghalayini 2',
                'email' => 'ghalayinisaleh69@gmail.com',
            ],
            [
                'name' => 'Rami Ghalayini',
                'email' => 'rghalayini21@gmail.com',
            ],
        ];

        foreach ($testUsers as $userData) {
            User::updateOrCreate(
                [
                    'email' => $userData['email']
                ],
                [
                    'name' => $userData['name'],
                    'password' => Hash::make('password'),
                    'role' => 'employee',
                    'company_id' => $company->id,
                    'active' => true,
                ]
            );
        }
    }
}
