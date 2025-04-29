<?php

namespace App;

use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Access\AuthorizationException;

trait AuthorizationTrait
{
    public function authorizeRole(array $allowedRoles)
    {
        $user = Auth::user();

        if (!$user || !in_array($user->role, $allowedRoles))
            throw new AuthorizationException('You are not authorized to perform this action.');
    }

    protected function isRole(string $role): bool
    {
        return Auth::user()?->role === $role;
    }

    protected function isAnyRole(array $roles): bool
    {
        return in_array(Auth::user()?->role, $roles);
    }
}
