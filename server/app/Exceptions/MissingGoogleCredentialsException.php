<?php

namespace App\Exceptions;

use Exception;

class MissingGoogleCredentialsException extends Exception
{
    protected $message = 'Google Calendar credentials not configured';
}
