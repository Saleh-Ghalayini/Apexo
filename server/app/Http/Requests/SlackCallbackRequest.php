<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SlackCallbackRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'code' => 'required|string',
            'state' => 'required|string',
        ];
    }
}
