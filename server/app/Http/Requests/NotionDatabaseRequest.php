<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NotionDatabaseRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'database_id' => 'required|string',
        ];
    }
}
