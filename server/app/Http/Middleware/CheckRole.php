<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, $minRole = null): Response
    {
        if (!$request->user())  return response()->json(['message' => 'Unauthenticated.'], 401);

        $userRole = $request->user()->role;

        if ($userRole === 'hr') return $next($request);

        if ($minRole === 'hr' && $userRole !== 'hr')    return response()->json(['message' => 'Unauthorized. HR access required.'], 403);

        if ($minRole === 'manager' && !in_array($userRole, ['manager', 'hr']))  return response()->json(['message' => 'Unauthorized. Manager access required.'], 403);

        if ($minRole === 'employee' && !in_array($userRole, ['employee', 'manager', 'hr']))  return response()->json(['message' => 'Unauthorized. Employee access required.'], 403);

        return $next($request);
    }
}
