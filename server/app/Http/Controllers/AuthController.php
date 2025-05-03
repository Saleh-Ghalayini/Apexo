<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AuthService;
use App\Traits\ResponseTrait;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\LogoutRequest;
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
            return $this->errorResponse($loginResponse[''], 401);

        return $this->successResponse($loginResponse);
    }
}
