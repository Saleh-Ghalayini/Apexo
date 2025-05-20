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

        $manager2 = User::factory()->create([
            'name' => 'Manager Company 2',
            'email' => 'manager@secondtest.com',
            'password' => Hash::make('password'),
            'role' => 'manager',
            'company_id' => $company2->id,
        ]);

        $employees2 = User::factory(3)->create([
            'company_id' => $company2->id,
            'role' => 'employee',
        ]);

        $meetings = \App\Models\Meeting::factory(5)->create([
            'user_id' => $manager2->id,
        ]);

        $users2 = collect([$hr2, $manager2])->concat($employees2);
        foreach ($users2 as $user) {
            \App\Models\Task::factory(rand(2, 5))->create([
                'user_id' => $user->id,
            ]);
        }

        foreach ($users2 as $user) {
            $chatSessions = \App\Models\ChatSession::factory(2)->create([
                'user_id' => $user->id,
            ]);

            foreach ($chatSessions as $session) {
                for ($i = 0; $i < rand(3, 6); $i++) {
                    \App\Models\ChatMessage::factory()->create([
                        'chat_session_id' => $session->id,
                        'role' => $i % 2 === 0 ? 'user' : 'assistant',
                    ]);
                }
            }
        }

        $this->command->info('Created test data for second company with HR restrictions testing');
    }
}
