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

            $userData = [
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role' => $data['role'],
                'company_id' => $company->id,
                'active' => true,
            ];

            $optionalFields = ['job_title', 'department', 'phone', 'avatar'];
            foreach ($optionalFields as $field) {
                if (isset($data[$field])) {
                    $userData[$field] = $data[$field];
                }
            }

            $user = User::create($userData);
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
