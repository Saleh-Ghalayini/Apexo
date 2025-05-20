<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GenerateEmailRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'user_name' => 'required|string',
            'user_email' => 'required|email',
            'task_title' => 'required|string',
            'task_details' => 'required|string',
            'deadline' => 'required|date',
        ];
    }
}
