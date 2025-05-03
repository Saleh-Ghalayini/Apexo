<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;


class AuthService
{
    public function login(array $credentials)
    {
        $auth_credentials = [
            'email' => $credentials['email'],
            'password' => $credentials['password'],
        ];

        if (!$token = JWTAuth::attempt($auth_credentials))
            return [
                'success' => false,
                'message' => 'Invalid Credentials',
            ];

        $user = Auth::user();

        return [
            'success' => true,
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ]
        ];
    }

    public function register(array $user_data)
    {
        $user_data['password'] = Hash::make($user_data['password']);

        $user = User::create($user_data);

        return [
            'success' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ],
        ];
    }
}
