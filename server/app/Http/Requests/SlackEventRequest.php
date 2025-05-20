<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SlackEventRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'type' => 'required|string',
            // Add more rules as needed for event structure
        ];
    }
}
