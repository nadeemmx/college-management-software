<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user || $user->status !== 'Active') {
            abort(403, 'Account is inactive or unauthorized.');
        }

        if ($user->role?->slug === 'super_admin') {
            return $next($request);
        }

        if (! $user->hasRole(...$roles)) {
            abort(403, 'You do not have permission to access this module.');
        }

        return $next($request);
    }
}

