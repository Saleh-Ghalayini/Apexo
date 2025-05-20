<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class HrService
{
    public function getAllEmployees()
    {
        $companyId = Auth::user()->company_id;
        return User::where('company_id', $companyId)->get();
    }

    public function searchEmployeesByName(string $name)
    {
        $companyId = Auth::user()->company_id;
        return User::where('company_id', $companyId)
            ->where('name', 'LIKE', "%{$name}%")
            ->get();
    }

    public function getEmployeeById(int $id)
    {
        $companyId = Auth::user()->company_id;
        return User::where('id', $id)
            ->where('company_id', $companyId)
            ->first();
    }
}
