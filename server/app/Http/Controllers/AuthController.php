<?php

namespace App\Http\Controllers;


use Exception;
use App\Traits\ResponseTrait;
use App\Services\AuthService;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    use ResponseTrait;
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register(RegisterRequest $request)
    {
        $validated = $request->validated();

        try {
            $data = $this->authService->register($validated);

            return $this->successResponse($data, 201);
        } catch (Exception $e) {
            Log::error('Registration error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse('Failed to register user: ' . $e->getMessage(), 500);
        }
    }

    public function login(LoginRequest $request)
    {
        $validated = $request->validated();

        try {
            $data = $this->authService->login($validated);

            return $this->successResponse($data);
        } catch (ValidationException $e) {
            throw $e;
        } catch (Exception $e) {
            Log::error('Login error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw ValidationException::withMessages([
                'email' => [$e->getMessage()],
            ]);
        }
    }

    public function logout()
    {
        try {
            $this->authService->logout();

            return $this->successResponse([
                'message' => 'Successfully logged out'
            ]);
        } catch (Exception $e) {
            Log::error('Logout error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse('Failed to logout', 500);
        }
    }

    public function refresh()
    {
        try {
            $data = $this->authService->refresh();

            return $this->successResponse($data);
        } catch (Exception $e) {
            Log::error('Token refresh error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if (
                strpos($e->getMessage(), 'User is not active') !== false ||
                strpos($e->getMessage(), 'Token') !== false
            ) {
                return $this->errorResponse('Unauthorized', 401);
            }

            return $this->errorResponse('Failed to refresh token', 500);
        }
    }
}
