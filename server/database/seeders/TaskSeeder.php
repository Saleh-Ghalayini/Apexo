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
    }
}
