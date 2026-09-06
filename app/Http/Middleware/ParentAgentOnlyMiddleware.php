<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ParentAgentOnlyMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->check()) {
            abort(401);
        }

        $user = auth()->user();

        if ($user->isSubAgent()) {
            abort(403, 'Access denied. Team management and agency financials are restricted to the primary agent.');
        }

        return $next($request);
    }
}
