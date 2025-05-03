<?php

namespace App\Http\Controllers;

use App\Services\AuthService;
use App\Traits\ResponseTrait;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;

class AuthController extends Controller
{
    use ResponseTrait;
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();

        $loginResponse = $this->authService->login($credentials);

        if (isset($loginResponse['success']) && !$loginResponse['success'])
            return $this->errorResponse($loginResponse['message'], 401);

        return $this->successResponse([
            'token' => $loginResponse['token'],
            'user' => $loginResponse['user']
        ]);
    }

    public function register(RegisterRequest $request)
    {
        $credentails = $request->validated();

        $registerResponse = $this->authService->register($credentails);

        if (isset($registerResponse['success']) && !$registerResponse['success'])
            return $this->errorResponse($registerResponse['message']);

        return $this->successResponse(['user' => $registerResponse['user']], 201);
    }

    public function logout()
    {
        $logoutResponse = $this->authService->logout();

        if (isset($logoutResponse['success']) && !$logoutResponse['success'])
            return $this->errorResponse($logoutResponse['message'], 400);

        return $this->successResponse(['message' => 'Successfully logged out']);
    }
}
