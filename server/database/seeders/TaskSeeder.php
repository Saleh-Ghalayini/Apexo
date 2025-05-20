<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    public function run(): void
    {
        $emails = [
            'ghalayinisaleh4@gmail.com',
            'ghalayinisaleh69@gmail.com',
            'rghalayini21@gmail.com',
        ];
        $users = User::whereIn('email', $emails)->get();

        foreach ($users as $user) {
            for ($i = 1; $i <= 2; $i++) {
                Task::create([
                    'user_id' => $user->id,
                    'assignee_id' => $user->id,
                    'title' => "Test Task $i for {$user->name}",
                    'description' => 'This is a test task for reminder system.',
                    'deadline' => Carbon::now()->addMinutes($i === 1 ? 1 : 5),
                    'status' => 'pending',
                    'priority' => 'high',
                ]);
            }
        }
    }
}
