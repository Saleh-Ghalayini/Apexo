<?php

use App\Http\Controllers\GoogleCalendarController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/google-calendar/events', [GoogleCalendarController::class, 'listEvents']);
Route::post('/google-calendar/events', [GoogleCalendarController::class, 'createEvent']);
Route::get('/google-calendar/redirect', [GoogleCalendarController::class, 'redirectToGoogle']);
Route::get('/google-calendar/callback', [GoogleCalendarController::class, 'handleGoogleCallback']);
