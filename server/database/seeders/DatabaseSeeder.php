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

        $employees = \App\Models\User::factory(5)->create([
            'company_id' => $company->id,
            'role' => 'employee',
        ]);

        $integration = \App\Models\Integration::factory()->create([
            'user_id' => $manager->id,
            'provider' => 'google_calendar',
        ]);

        $meetings = \App\Models\Meeting::factory(10)->create([
            'user_id' => $manager->id,
        ]);

        $users = collect([$admin, $manager, $employee])->concat($employees);
        foreach ($users as $user) {
            \App\Models\Task::factory(rand(3, 8))->create([
                'user_id' => $user->id,
            ]);
        }

        foreach ($users as $user) {
            $chatSessions = \App\Models\ChatSession::factory(rand(1, 3))->create([
                'user_id' => $user->id,
            ]);

            foreach ($chatSessions as $session) {
                for ($i = 0; $i < rand(3, 10); $i++) {
                    \App\Models\ChatMessage::factory()->create([
                        'chat_session_id' => $session->id,
                        'role' => $i % 2 === 0 ? 'user' : 'assistant',
                    ]);
                }
            }
        }

        $this->call(ChatAITestDataSeeder::class);

        if (app()->environment(['local', 'development', 'testing']))
            $this->call(TestMultiCompanySeeder::class);

        $this->call(TestUserSeeder::class);
        $this->call(TaskSeeder::class);
    }
}
