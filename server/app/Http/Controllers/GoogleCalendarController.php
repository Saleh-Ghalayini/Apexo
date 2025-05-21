<?php

namespace App\Http\Controllers;

use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Services\GoogleCalendarService;
use App\Http\Requests\GoogleCalendarEventRequest;

class GoogleCalendarController extends Controller
{
    use ResponseTrait;

    protected $calendarService;

    public function __construct(GoogleCalendarService $calendarService)
    {
        $this->calendarService = $calendarService;
    }

    public function redirectToGoogle(Request $request)
    {
        $token = $request->query('jwt');
        Log::info('Google OAuth redirectToGoogle received jwt', ['jwt' => $token]);
        if (!$token && $request->hasHeader('Authorization'))
            if (preg_match('/Bearer\s+(.*)$/i', $request->header('Authorization'), $matches))
                $token = $matches[1];

        // Check for missing credentials file
        $credentialsPath = config('services.google_calendar.credentials_path');
        if (!file_exists(base_path($credentialsPath))) {
            return $this->errorResponse('Google Calendar credentials not configured', 422);
        }

        $authUrl = $this->calendarService->getAuthUrlWithState($token);
        Log::info('Google OAuth redirect URL', ['authUrl' => $authUrl, 'state' => $token]);
        if (!$request->expectsJson() && !str_contains($request->header('accept', ''), 'application/json'))
            return redirect()->away($authUrl);

        return $this->successResponse([
            'authUrl' => $authUrl,
            'state' => $token
        ]);
    }

    public function handleGoogleCallback(Request $request)
    {
        Log::info('Google OAuth callback query', $request->query());
        if ($request->query('error')) return $this->errorResponse('Authorization failed: ' . $request->query('error'), 400);

        $code = $request->query('code');
        $state = $request->query('state');
        Log::info('Google OAuth callback received', ['code' => $code, 'state' => $state]);
        if (!$code) return $this->errorResponse('Missing code', 400);

        $user = null;
        if ($state) {
            try {
                $payload = app('tymon.jwt.auth')->setToken($state)->getPayload();
                $userId = $payload->get('sub');
                $user = \App\Models\User::find($userId);
            } catch (\Exception $e) {
                Log::error('JWT decode failed', ['message' => $e->getMessage(), 'state' => $state]);
            }
        } else {
            $user = Auth::user();
        }

        try {
            $token = $this->calendarService->fetchAccessTokenWithAuthCode($code);
        } catch (\Exception $e) {
            Log::error('Error fetching access token', ['message' => $e->getMessage()]);
            return $this->errorResponse('Token exchange failed', 400);
        }

        if ($user && isset($token['access_token'])) {
            $user->google_calendar_token = $token;
            $user->save();
            return $this->successResponse([
                'message' => 'Google Calendar connected!'
            ]);
        }

        Log::error('Could not connect Google Calendar', ['user' => $user, 'token' => $token]);
        return $this->errorResponse('Could not connect Google Calendar', 400);
    }

    public function listEvents(Request $request)
    {
        $user = Auth::user();
        // Check for missing credentials file
        $credentialsPath = config('services.google_calendar.credentials_path');
        if (!file_exists(base_path($credentialsPath))) {
            return $this->errorResponse('Google Calendar credentials not configured', 422);
        }
        try {
            $this->calendarService->setUserTokenOrFail($user);
            $maxResults = $request->query('maxResults', 10);
            $events = $this->calendarService->listUpcomingEvents($maxResults);
            return $this->successResponse([
                'events' => $events
            ]);
        } catch (\Exception $e) {
            Log::error('Google Calendar listEvents error', ['user_id' => $user?->id, 'error' => $e->getMessage()]);
            return $this->errorResponse($e->getMessage(), 401);
        }
    }

    public function createEvent(GoogleCalendarEventRequest $request)
    {
        $user = Auth::user();
        // Check for missing credentials file
        $credentialsPath = config('services.google_calendar.credentials_path');
        if (!file_exists(base_path($credentialsPath))) {
            return $this->errorResponse('Google Calendar credentials not configured', 422);
        }
        try {
            $this->calendarService->setUserTokenOrFail($user);
            $eventData = $request->validated();
            $eventData = $this->calendarService->cleanEventData($eventData);
            $event = $this->calendarService->createEvent($eventData);
            return $this->successResponse([
                'event' => $event
            ]);
        } catch (\Google_Service_Exception $e) {
            Log::error('Google Calendar API error', ['user_id' => $user?->id, 'error' => $e->getMessage()]);
            return $this->errorResponse('Google Calendar API error: ' . $e->getMessage(), 400);
        } catch (\Exception $e) {
            Log::error('Google Calendar createEvent error', ['user_id' => $user?->id, 'error' => $e->getMessage()]);
            return $this->errorResponse($e->getMessage(), 401);
        }
    }

    public function aiBookEvent(Request $request)
    {
        $validated = $request->validate([
            'text' => 'required|string',
        ]);
        $user = Auth::user();
        $token = $user ? $user->google_calendar_token : null;
        if (!$token) return $this->errorResponse('Not authenticated with Google Calendar', 401);

        $this->calendarService->setAccessToken($token);
        $text = $validated['text'];
        if (preg_match('/book a (\d+)hr (.+) for me/i', $text, $matches)) {
            $hours = (int)$matches[1];
            $topic = trim($matches[2]);
            $start = now()->addHour();
            $end = $start->copy()->addHours($hours);
            $eventData = [
                'summary' => $topic,
                'start' => ['dateTime' => $start->toIso8601String(), 'timeZone' => config('app.timezone', 'UTC')],
                'end' => ['dateTime' => $end->toIso8601String(), 'timeZone' => config('app.timezone', 'UTC')],
            ];
            $event = $this->calendarService->createEvent($eventData);
            return $this->successResponse([
                'message' => 'Event booked!',
                'event' => $event
            ]);
        }
        return $this->errorResponse('Could not parse request. Please use: "book a 1hr [topic] for me in my calendar"', 400);
    }

    public function saveToken(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string',
        ]);
        $user = Auth::user();
        if (!$user) return $this->errorResponse('Missing user', 400);

        $token = $this->calendarService->fetchAccessTokenWithAuthCode($validated['code']);
        if ($user instanceof \App\Models\User) {
            $user->google_calendar_token = $token;
            $user->save();
            return $this->successResponse([
                'message' => 'Token saved!'
            ]);
        }
        return $this->errorResponse('User model not found or invalid', 400);
    }
}
