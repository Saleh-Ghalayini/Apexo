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

        $token = JWTAuth::fromUser($user);

        return [
            'user' => $user,
            'company' => $company,
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => config('jwt.ttl') * 60,
        ];
    }

    public function login(array $credentials)
    {
        try {
            if (!$token = auth('api')->attempt($credentials)) {
                throw new Exception('The provided credentials are incorrect.');
            }

            $user = auth('api')->user();

            if (!$user->active) {
                auth('api')->logout();
                throw new Exception('This account has been deactivated.');
            }

            return [
                'user' => $user,
                'token' => $token,
                'token_type' => 'bearer',
                'expires_in' => config('jwt.ttl') * 60,
            ];
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function logout()
    {
        try {
            auth('api')->logout();
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function getAuthUserWithToken()
    {
        try {
            $user = auth('api')->user();
            if (!$user) throw new Exception('User not authenticated');

            $token = JWTAuth::fromUser($user);

            return [
                'user' => $user,
                'token' => $token,
                'token_type' => 'bearer',
                'expires_in' => config('jwt.ttl') * 60,
            ];
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function refresh()
    {
        try {
            $user = auth('api')->user();
            if (!$user || !$user->active) throw new Exception('User is not active.');

            $token = JWTAuth::parseToken()->refresh();

            return [
                'user' => $user,
                'token' => $token,
                'token_type' => 'bearer',
                'expires_in' => config('jwt.ttl') * 60,
            ];
        } catch (Exception $e) {
            throw $e;
        }
    }
}
