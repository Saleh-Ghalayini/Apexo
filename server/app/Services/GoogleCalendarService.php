<?php

namespace App\Services;

use Google_Client;
use Google\Service\Calendar as Google_Service_Calendar;
use Google\Service\Calendar\Event as Google_Service_Calendar_Event;

class GoogleCalendarService
{
    protected $client;
    protected $calendarService;

    public function __construct()
    {
        $this->client = new Google_Client();
        $credentialsPath = config('services.google_calendar.credentials_path');
        if (!file_exists(base_path($credentialsPath))) {
            throw new \App\Exceptions\MissingGoogleCredentialsException();
        }
        $this->client->setAuthConfig(base_path($credentialsPath));
        $this->client->setScopes([
            Google_Service_Calendar::CALENDAR,
        ]);
        $this->client->setAccessType('offline');
        $this->client->setRedirectUri(env('GOOGLE_REDIRECT_URI'));
        $this->calendarService = new Google_Service_Calendar($this->client);
    }

    public function getAuthUrl($state = null)
    {
        if ($state)
            return $this->client->createAuthUrl(['state' => $state]);

        return $this->client->createAuthUrl();
    }

    public function getAuthUrlWithState($state = null)
    {
        $this->client->setScopes([Google_Service_Calendar::CALENDAR]);
        if ($state)
            $this->client->setState($state);

        return $this->client->createAuthUrl();
    }

    public function fetchAccessTokenWithAuthCode($code)
    {
        $token = $this->client->fetchAccessTokenWithAuthCode($code);
        return $token;
    }

    public function setAccessToken($token)
    {
        $this->client->setAccessToken($token);
    }

    public function listUpcomingEvents($maxResults = 10)
    {
        $calendarId = 'primary';
        $optParams = [
            'maxResults' => $maxResults,
            'orderBy' => 'startTime',
            'singleEvents' => true,
            'timeMin' => date('c'),
        ];
        $results = $this->calendarService->events->listEvents($calendarId, $optParams);
        return $results->getItems();
    }

    public function createEvent($eventData)
    {
        $calendarId = 'primary';
        $event = new Google_Service_Calendar_Event($eventData);
        return $this->calendarService->events->insert($calendarId, $event);
    }

    public function setUserTokenOrFail($user)
    {
        $token = $user ? $user->google_calendar_token : null;
        if (!$token) {
            throw new \Exception('Not authenticated with Google Calendar');
        }
        $this->setAccessToken($token);
    }

    public function cleanEventData(array $eventData): array
    {
        if (isset($eventData['attendees']) && is_array($eventData['attendees'])) {
            $eventData['attendees'] = array_values(array_filter($eventData['attendees'], function ($attendee) {
                return isset($attendee['email']) && filter_var($attendee['email'], FILTER_VALIDATE_EMAIL);
            }));
            if (empty($eventData['attendees'])) {
                unset($eventData['attendees']);
            }
        }
        return $eventData;
    }
}
