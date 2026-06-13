<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MarketerMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->check()) {
            abort(403);
        }

        $role = strtoupper(auth()->user()->role ?? '');
        $allowed = ['MARKETER', 'ADMIN', 'SUPER_ADMIN', 'SUB-ADMIN'];

        if (! in_array($role, $allowed)) {
            abort(403, 'Unauthorized. Marketer or Admin access required.');
        }

        return $next($request);
    }
}
