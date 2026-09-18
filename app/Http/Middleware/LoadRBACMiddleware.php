<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

class LoadRBACMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();

            if (!$user->relationLoaded('assignedRole')) {
                $user->load('assignedRole.permissions');
            }

            $permissionNames = $user->assignedRole
                ? $user->assignedRole->permissions->pluck('name')->toArray()
                : [];

            View::share('userPermissions', $permissionNames);
            View::share('userRole', $user->assignedRole);
        }

        return $next($request);
    }
}
