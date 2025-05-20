<?php

namespace App\Traits;

trait MessageAnalysisTrait
{
    protected function messageNeedsStructuredOutput(string $message): bool
    {
        $taskKeywords = ['task', 'todo', 'to-do', 'to do', 'assign', 'action item', 'deliverable'];

        $message = strtolower($message);

        foreach ($taskKeywords as $keyword) {
            if (str_contains($message, $keyword))   return true;
        }
        return false;
    }
}
