<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GoogleCalendarEventRequest extends FormRequest
{
    public function authorize()
    {
        // You can add authorization logic here if needed
        return true;
    }

    public function rules()
    {
        return [
            'summary' => 'required|string',
            'start' => 'required|array',
            'end' => 'required|array',
            // Optionally add more rules for description, attendees, etc.
        ];
    }
}
