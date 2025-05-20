<?php

namespace App\Jobs;

use App\Models\Meeting;
use App\Services\AIService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldQueue;

class ProcessMeetingAnalyticsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $meetingId;

    public function __construct($meetingId)
    {
        $this->meetingId = $meetingId;
    }

    public function handle(AIService $aiService)
    {
        $meeting = Meeting::find($this->meetingId);
        if (!$meeting || !$meeting->transcript) return;

        $analytics = $aiService->analyzeMeetingTranscript($meeting->transcript, $meeting->toArray());
        if (isset($analytics['summary']))   $meeting->summary = $analytics['summary'];

        $meeting->analytics = $analytics;
        $meeting->save();
    }
}
