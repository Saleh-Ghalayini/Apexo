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
    }
}
