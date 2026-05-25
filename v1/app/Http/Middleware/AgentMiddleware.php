<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AgentMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || auth()->user()->role !== 'AGENT') {
            abort(403);
        }

        if (!auth()->user()->is_active) {
            abort(403, 'Your account is not active.');
        }

        return $next($request);
    }
}
