<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class SendAnnouncementRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Only allow if user is authenticated
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'slack_channel' => 'required|string',
        ];
    }
}
