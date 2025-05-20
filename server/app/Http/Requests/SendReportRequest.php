<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendReportRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'report' => 'required|string',
            'to' => 'required|email',
            'from_user_id' => 'required|integer',
        ];
    }
}
