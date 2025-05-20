<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AIController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\NotionController;
use App\Http\Controllers\MeetingController;
use App\Http\Controllers\SlackOAuthController;
use App\Http\Controllers\SlackEventController;
use App\Http\Controllers\NotionOAuthController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\IntegrationsController;
use App\Http\Controllers\GoogleCalendarController;

Route::group(['prefix' => 'v1'], function () {

    Route::group(['prefix' => 'auth'], function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:api');
        Route::post('/refresh', [AuthController::class, 'refresh'])->middleware('auth:api');
    });

    Route::middleware('auth:api')->get('/user', [UserController::class, 'getUser']);

    // HR User Routes
    Route::middleware(['auth:api', 'hr_only'])->group(function () {
        Route::get('/employees/{id}', [UserController::class, 'getEmployeeById'])->where('id', '[0-9]+');
        Route::get('/employees', [UserController::class, 'getEmployeesByCompany']);
        Route::get('/employees/search', [UserController::class, 'searchEmployeesByName']);
    });

    // Chat routes
    Route::middleware('auth:api')->group(function () {
        Route::get('/chat/sessions', [ChatController::class, 'getSessions']);
        Route::post('/chat/sessions', [ChatController::class, 'createSession']);
        Route::get('/chat/sessions/{id}', [ChatController::class, 'getSession']);
        Route::delete('/chat/sessions/{id}', [ChatController::class, 'deleteSession']);
        Route::post('/chat/sessions/{id}/messages', [ChatController::class, 'sendMessage']);
        Route::post('/chat/sessions/{id}/archive', [ChatController::class, 'archiveSession']);
    });

    // OAuth routes — no auth middleware but need session
    Route::middleware(['web'])->group(function () {
        Route::get('/integrations/slack/redirect', [SlackOAuthController::class, 'redirectToSlack']);
        Route::get('/integrations/notion/redirect', [NotionOAuthController::class, 'redirectToNotion']);
        Route::get('/integrations/notion/authorize', [NotionOAuthController::class, 'redirectToNotion']);
        Route::get('/integrations/slack/callback', [SlackOAuthController::class, 'handleSlackCallback'])->name('slack.callback');
        Route::get('/integrations/notion/callback', [NotionOAuthController::class, 'handleNotionCallback'])->name('notion.callback');
    });

    // Slack events route
    Route::post('/slack/events', [SlackEventController::class, 'handle']);

    // Announcements — requires auth
    Route::middleware('auth:api')->post('/announcements/slack', [AnnouncementController::class, 'sendToSlack']);

    // Integrations API for frontend
    Route::middleware('auth:api')->group(function () {
        Route::post('/ai/send-report', [AIController::class, 'sendReport']);
        Route::get('/notion/databases', [NotionController::class, 'getDatabases']);
        Route::get('/integrations', [IntegrationsController::class, 'getIntegrations']);
        Route::patch('/notion/pages/{pageId}', [NotionController::class, 'updatePage']);
        Route::get('/ai/generate-task-report', [AIController::class, 'generateTaskReport']);
        Route::get('/notion/saved-databases', [NotionController::class, 'getSavedDatabases']);
        Route::get('/integrations/providers', [IntegrationsController::class, 'getProviders']);
        Route::get('/notion/databases/{databaseId}', [NotionController::class, 'getDatabase']);
        Route::post('/notion/databases/{databaseId}/pages', [NotionController::class, 'createPage']);
        Route::post('/integrations/connect/{providerId}', [IntegrationsController::class, 'connect']);
        Route::delete('/integrations/{integrationId}', [IntegrationsController::class, 'disconnect']);
        Route::post('/notion/databases/{databaseId}/save', [NotionController::class, 'saveDatabase']);
        Route::post('/notion/databases/{databaseId}/query', [NotionController::class, 'queryDatabase']);
        Route::patch('/integrations/{integrationId}/status', [IntegrationsController::class, 'updateStatus']);
    });

    // Google Calendar Integration (API)
    Route::get('/google-calendar/redirect', [GoogleCalendarController::class, 'redirectToGoogle']);
    Route::get('/google-calendar/callback', [GoogleCalendarController::class, 'handleGoogleCallback']);

    // New endpoint for saving token from frontend
    Route::middleware('auth:api')->post('/google-calendar/save-token', [GoogleCalendarController::class, 'saveToken']);

    // The following routes require authentication
    Route::middleware('auth:api')->group(function () {
        Route::get('/google-calendar/events', [GoogleCalendarController::class, 'listEvents']);
        Route::post('/google-calendar/events', [GoogleCalendarController::class, 'createEvent']);
        Route::post('/google-calendar/ai-book', [GoogleCalendarController::class, 'aiBookEvent']);

        // Meeting routes
        Route::post('/meetings', [MeetingController::class, 'store']);
        Route::get('/meetings/{id}', [MeetingController::class, 'show']);
        Route::patch('/meetings/{id}', [MeetingController::class, 'update']);

        // Analytics endpoints
        Route::post('/ai/analyze-meeting/{meetingId}', [AIController::class, 'analyzeMeeting']);
        Route::post('/ai/analyze-employee/{employeeId}', [AIController::class, 'analyzeEmployee']);
        Route::get('/ai/meeting-report/{meetingId}', [AIController::class, 'downloadMeetingReport']);
        Route::get('/ai/meeting-analytics/{meetingId}', [AIController::class, 'getMeetingAnalytics']);
        Route::get('/ai/employee-analytics/{employeeId}', [AIController::class, 'getEmployeeAnalytics']);
        Route::get('/ai/employee-report/{employeeAnalyticsId}', [AIController::class, 'downloadEmployeeReport']);

        // Task stub route for test
        Route::post('/tasks', function () {
            return response()->json(['message' => 'Created'], 201);
        });

        Route::middleware('auth:api')->post('/ai/send-reminder', [AIController::class, 'sendReminder']);
    });
});
