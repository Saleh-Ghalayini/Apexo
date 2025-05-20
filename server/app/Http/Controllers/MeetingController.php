<?php

namespace App\Http\Controllers;

use App\Models\Meeting;
use App\Jobs\ProcessMeetingAnalyticsJob;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;

class MeetingController extends Controller
{
    use ResponseTrait;

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'title' => 'required|string',
            'scheduled_at' => 'nullable|date',
            'ended_at' => 'nullable|date',
            'transcript' => 'nullable|string',
            'summary' => 'nullable|string',
            'status' => 'nullable|string',
            'external_id' => 'nullable|string',
            'meeting_url' => 'nullable|string',
            'attendees' => 'nullable|array',
            'metadata' => 'nullable|array',
        ]);

        $existing = Meeting::where('user_id', $data['user_id'])
            ->where('title', $data['title'])
            ->where('scheduled_at', $data['scheduled_at'])
            ->first();
        if ($existing) return $this->successResponse($existing, 200);

        if (isset($data['attendees']) && is_array($data['attendees']))
            $data['attendees'] = array_map(function ($attendee) {
                if (filter_var($attendee, FILTER_VALIDATE_EMAIL)) return ['email' => $attendee];

                return ['name' => $attendee];
            }, $data['attendees']);

        $meeting = Meeting::create($data);
        if (!empty($data['transcript'])) ProcessMeetingAnalyticsJob::dispatch($meeting->id);

        return $this->successResponse($meeting, 201);
    }

    public function update(Request $request, $id)
    {
        $meeting = Meeting::findOrFail($id);
        if ($request->user()->id !== $meeting->user_id) return response()->json(['message' => 'Forbidden'], 403);
        $data = $request->validate([
            'title' => 'sometimes|string',
            'scheduled_at' => 'sometimes|date',
            'ended_at' => 'sometimes|date',
            'transcript' => 'sometimes|string',
            'summary' => 'sometimes|string',
            'status' => 'sometimes|string',
            'external_id' => 'sometimes|string',
            'meeting_url' => 'sometimes|string',
            'attendees' => 'sometimes|array',
            'metadata' => 'sometimes|array',
        ]);
        $meeting->update($data);
        if (array_key_exists('transcript', $data) && !empty($data['transcript']))
            ProcessMeetingAnalyticsJob::dispatch($meeting->id);

        return $this->successResponse($meeting);
    }

    public function show($id)
    {
        $meeting = Meeting::findOrFail($id);
        return $this->successResponse($meeting);
    }
}
