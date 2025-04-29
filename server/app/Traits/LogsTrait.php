<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;

trait LogsTrait
{
    protected function logInfo(string $message, array $context = [])
    {
        Log::info($message, $context);
    }

    protected function logError(string $message, array $context = [])
    {
        Log::error($message, $context);
    }

    protected function logDebug(string $message, array $context = [])
    {
        Log::debug($message, $context);
    }
}
