<?php

namespace App\Exports;

use App\Models\Meeting;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;

class MeetingReportExport implements FromArray, WithTitle
{
    protected $meeting;
    protected $analytics;

    public function __construct(Meeting $meeting, $analytics)
    {
        $this->meeting = $meeting;
        $this->analytics = $analytics;
    }

    public function array(): array
    {
        return [
            ['Meeting Report'],
            ['Title', $this->meeting->title],
            ['Scheduled At', $this->meeting->scheduled_at],
            ['Ended At', $this->meeting->ended_at],
            ['Summary', $this->analytics['summary'] ?? ''],
            ['Sentiment', $this->analytics['sentiment'] ?? ''],
            ['Topics', isset($this->analytics['topics']) ? implode(', ', $this->analytics['topics']) : ''],
            ['Action Items'],
            ...collect($this->analytics['action_items'] ?? [])->map(function ($item) {
                return [
                    $item['description'] ?? '',
                    $item['assignee'] ?? '',
                    $item['due_date'] ?? ''
                ];
            })->toArray(),
        ];
    }

    public function title(): string
    {
        return 'Meeting Report';
    }
}
