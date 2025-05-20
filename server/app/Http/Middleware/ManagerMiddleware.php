<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ManagerMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user())  return response()->json(['message' => 'Unauthenticated.'], 401);

        if (in_array($request->user()->role, ['manager', 'hr']))    return $next($request);

        return response()->json(['message' => 'Unauthorized. Manager access required.'], 403);
    }
}
