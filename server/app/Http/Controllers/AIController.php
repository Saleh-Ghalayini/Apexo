<?php

namespace App\Http\Controllers;

use App\Services\AIService;
use App\Traits\ResponseTrait;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\ChatRequest;
use Illuminate\Support\Facades\Auth;

class AIController extends Controller
{
    use ResponseTrait;
    protected $aiService;

    public function __construct(AIService $aiService)
    {
        $this->aiService = $aiService;
    }

    public function chat(ChatRequest $request): JsonResponse
    {
        $user = Auth::user();
        $prompt = $request->validated()['prompt'];

        $response = $this->aiService->handlePrompt($user, $prompt);

        return $this->successResponse([
            'response' => $response,
        ]);
    }
}
