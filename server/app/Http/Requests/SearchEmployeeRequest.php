<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SearchEmployeeRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Use middleware for role checks
    }

    public function rules()
    {
        return [
            'name' => 'required|string|min:2',
        ];
    }
}
