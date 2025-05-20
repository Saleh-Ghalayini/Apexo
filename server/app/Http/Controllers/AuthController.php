<?php

namespace App\Http\Controllers;

use App\Services\AuthService;

class AuthController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register($request)
    {
        // To be implemented
    }

    public function login($request)
    {
        // To be implemented
    }
}
