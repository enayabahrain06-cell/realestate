<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        if (!$request->user()) {
            return redirect()->route('login');
        }

        // Load relationships for permission check
        $user = $request->user()->load('realEstateRoles.permissions');

        if (!$user->hasPermission($permission)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Unauthorized. You do not have the required permission.'
                ], 403);
            }

            abort(403, 'Unauthorized action. You do not have permission: ' . $permission);
        }

        return $next($request);
    }
}

