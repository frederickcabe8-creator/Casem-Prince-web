<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!$request->user()) {
            abort(403, 'Unauthorized.');
        }

        if (!method_exists($request->user(), 'hasAnyRole')) {
            abort(403, 'Role system not available.');
        }

        if (!$request->user()->hasAnyRole($roles)) {
            abort(403, 'Unauthorized.');
        }

        return $next($request);
    }
}