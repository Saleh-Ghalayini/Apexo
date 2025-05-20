<?php

// This file is deprecated. All feature tests are now split by feature in their own files.
// SKIP ALL TESTS IN THIS FILE. It is safe to delete this file if you no longer need it.

namespace Tests\Feature;

use App\Models\User;
use App\Models\Meeting;
use App\Models\Task;
use App\Models\Integration;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class MainFeaturesTest extends TestCase
{
    use DatabaseTransactions;

    // SKIPPED: test_meeting_creation_and_fetch
    // SKIPPED: test_task_creation_with_factory
    // SKIPPED: test_integration_connect_and_status_update
    // SKIPPED: test_hr_can_see_only_company_employees
    // SKIPPED: test_ai_generate_task_report
    // All tests in this file are now split by feature and should not be run here.
}
