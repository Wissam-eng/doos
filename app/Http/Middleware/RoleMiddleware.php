<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, $roles)
    {
        $user = Auth::guard('doos_users')->user();

        // حول السلسلة إلى مصفوفة أدوار
        $roleArray = explode('|', $roles);

        if (!$user || !in_array($user->role, $roleArray)) {
            return response()->json(['error' => 'Unauthorized.'], 403);
        }

        return $next($request);
    }
}

