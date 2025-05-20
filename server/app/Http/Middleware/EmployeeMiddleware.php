<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EmployeeMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user())  return response()->json(['message' => 'Unauthenticated.'], 401);

        if (in_array($request->user()->role, ['employee', 'manager', 'hr']))  return $next($request);

        return response()->json(['message' => 'Unauthorized. You do not have permission to access this resource.'], 403);
    }
}
