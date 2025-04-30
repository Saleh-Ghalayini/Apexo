<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Report;
use App\Models\Meeting;
use App\Models\Company;
use App\Models\ChatSession;
use App\Models\Integration;
use App\Models\SlackAnnouncement;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        Company::factory(5)->create()->each(function ($company) {
            // Create 10 users per company (split across roles)
            User::factory(3)->create([
                'company_id' => $company->id,
                'role' => 'employee',
            ]);

            User::factory(3)->create([
                'company_id' => $company->id,
                'role' => 'manager',
            ]);

            User::factory(2)->create([
                'company_id' => $company->id,
                'role' => 'hr',
            ]);

            // Create other company-related data
            Meeting::factory(3)->create(['company_id' => $company->id]);
            Report::factory(2)->create(['company_id' => $company->id]);
            Integration::factory(2)->create(['company_id' => $company->id]);
        });

        // For each user, create SlackAnnouncements and ChatSessions
        User::all()->each(function ($user) {
            SlackAnnouncement::factory(1)->create(['user_id' => $user->id]);
            ChatSession::factory(2)->create(['user_id' => $user->id]);
        });
    }
}
