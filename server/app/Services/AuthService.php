<?php

namespace App\Services;

use Exception;
use App\Models\User;
use App\Models\Company;
use Illuminate\Support\Facades\Hash;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class AuthService
{
    public function register(array $data)
    {
        try {
            $company = Company::firstOrCreate(
                ['domain' => $data['company_domain']],
                [
                    'name' => $data['company_name'],
                    'domain' => $data['company_domain'],
                    'status' => 'active',
                ]
            );
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function login(array $credentials)
    {
        // To be implemented
    }

    public function logout()
    {
        // To be implemented
    }

    public function getAuthUserWithToken()
    {
        // To be implemented
    }

    public function refresh()
    {
        // To be implemented
    }
}
