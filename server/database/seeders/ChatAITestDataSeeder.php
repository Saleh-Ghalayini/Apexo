<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Task;
use App\Models\Meeting;
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

        $hr = User::updateOrCreate(
            ['email' => 'hr@example.com'],
            [
                'company_id' => $company->id,
                'name' => 'HR Manager',
                'password' => Hash::make('password'),
                'role' => 'hr',
                'job_title' => 'HR Director',
                'department' => 'Human Resources',
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        $teamMember1 = User::updateOrCreate(
            ['email' => 'team1@example.com'],
            [
                'company_id' => $company->id,
                'name' => 'Team Member 1',
                'password' => Hash::make('password'),
                'role' => 'employee',
                'job_title' => 'Frontend Developer',
                'department' => 'Engineering',
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        $teamMember2 = User::updateOrCreate(
            ['email' => 'team2@example.com'],
            [
                'company_id' => $company->id,
                'name' => 'Team Member 2',
                'password' => Hash::make('password'),
                'role' => 'employee',
                'job_title' => 'Backend Developer',
                'department' => 'Engineering',
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        $salesManager = User::updateOrCreate(
            ['email' => 'sales-manager@example.com'],
            [
                'company_id' => $company->id,
                'name' => 'Sales Manager',
                'password' => Hash::make('password'),
                'role' => 'manager',
                'job_title' => 'Sales Director',
                'department' => 'Sales',
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        $salesRep = User::updateOrCreate(
            ['email' => 'sales@example.com'],
            [
                'company_id' => $company->id,
                'name' => 'Sales Representative',
                'password' => Hash::make('password'),
                'role' => 'employee',
                'job_title' => 'Sales Representative',
                'department' => 'Sales',
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        $this->createTasks($employee, $manager);
        $this->createTasks($teamMember1, $manager);
        $this->createTasks($teamMember2, $manager);
        $this->createTasks($salesRep, $salesManager);

        $this->createMeetings($employee, $manager);
        $this->createMeetings($manager, null, [$employee->id, $teamMember1->id, $teamMember2->id]);
        $this->createMeetings($hr, null, [$manager->id, $salesManager->id]);
        $this->createMeetings($salesManager, null, [$salesRep->id]);
    }

    private function createTasks(User $user, ?User $assignedBy = null): void
    {
        $statuses = ['pending', 'in_progress', 'completed'];
        $priorities = ['low', 'medium', 'high'];

        for ($i = 1; $i <= 3; $i++) {
            Task::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'title' => "Personal Task {$i} for {$user->name}"
                ],
                [
                    'assignee_id' => $user->id,
                    'description' => "This is a personal task {$i} for {$user->name}",
                    'deadline' => now()->addDays(rand(1, 14)),
                    'status' => $statuses[array_rand($statuses)],
                    'priority' => $priorities[array_rand($priorities)],
                    'source_type' => 'App\\Models\\User',
                    'source_id' => $user->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        if ($assignedBy) {
            for ($i = 1; $i <= 2; $i++) {
                Task::updateOrCreate(
                    [
                        'user_id' => $assignedBy->id,
                        'assignee_id' => $user->id,
                        'title' => "Task {$i} assigned to {$user->name} by {$assignedBy->name}"
                    ],
                    [
                        'description' => "This is task {$i} assigned to {$user->name} by {$assignedBy->name}",
                        'deadline' => now()->addDays(rand(1, 14)),
                        'status' => $statuses[array_rand($statuses)],
                        'priority' => $priorities[array_rand($priorities)],
                        'source_type' => 'App\\Models\\User',
                        'source_id' => $assignedBy->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }
        }
    }

    private function createMeetings(User $creator, ?User $with = null, array $attendeeIds = []): void
    {
        $attendees = [
            [
                'id' => $creator->id,
                'name' => $creator->name,
                'email' => $creator->email,
                'department' => $creator->department,
                'status' => 'confirmed'
            ]
        ];

        if ($with) {
            $attendees[] = [
                'id' => $with->id,
                'name' => $with->name,
                'email' => $with->email,
                'department' => $with->department,
                'status' => 'confirmed'
            ];
        }

        foreach ($attendeeIds as $attendeeId) {
            $attendee = User::find($attendeeId);
            if ($attendee) {
                $attendees[] = [
                    'id' => $attendee->id,
                    'name' => $attendee->name,
                    'email' => $attendee->email,
                    'department' => $attendee->department,
                    'status' => ['confirmed', 'pending'][rand(0, 1)]
                ];
            }
        }

        Meeting::updateOrCreate(
            [
                'user_id' => $creator->id,
                'title' => "Past Meeting by {$creator->name}"
            ],
            [
                'scheduled_at' => now()->subDays(3)->setTime(10, 0),
                'ended_at' => now()->subDays(3)->setTime(11, 0),
                'transcript' => "This is a transcript of the past meeting created by {$creator->name}.",
                'summary' => "Past meeting summary: discussed project status and next steps.",
                'status' => 'completed',
                'meeting_url' => 'https://meet.example.com/past-' . strtolower(str_replace(' ', '-', $creator->name)),
                'attendees' => $attendees,
                'metadata' => [
                    'platform' => 'Zoom',
                    'recording_available' => true
                ],
                'created_at' => now()->subDays(5),
                'updated_at' => now()->subDays(3),
            ]
        );

        Meeting::updateOrCreate(
            [
                'user_id' => $creator->id,
                'title' => "Today's Meeting by {$creator->name}"
            ],
            [
                'scheduled_at' => now()->setTime(15, 0),
                'ended_at' => null,
                'transcript' => null,
                'summary' => null,
                'status' => 'scheduled',
                'meeting_url' => 'https://meet.example.com/today-' . strtolower(str_replace(' ', '-', $creator->name)),
                'attendees' => $attendees,
                'metadata' => [
                    'platform' => 'Microsoft Teams',
                    'agenda' => 'Discuss current projects and blockers'
                ],
                'created_at' => now()->subDays(1),
                'updated_at' => now(),
            ]
        );

        Meeting::updateOrCreate(
            [
                'user_id' => $creator->id,
                'title' => "Future Planning Meeting by {$creator->name}"
            ],
            [
                'scheduled_at' => now()->addDays(5)->setTime(14, 30),
                'ended_at' => null,
                'transcript' => null,
                'summary' => null,
                'status' => 'scheduled',
                'meeting_url' => 'https://meet.example.com/future-' . strtolower(str_replace(' ', '-', $creator->name)),
                'attendees' => $attendees,
                'metadata' => [
                    'platform' => 'Google Meet',
                    'agenda' => 'Plan next quarter objectives and key results'
                ],
                'created_at' => now()->subDays(1),
                'updated_at' => now(),
            ]
        );
    }
}
