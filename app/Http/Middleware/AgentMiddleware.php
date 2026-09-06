<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AgentMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->check() || strtoupper(auth()->user()->role) !== 'AGENT') {
            abort(403);
        }

        $user = auth()->user();

        if (! $user->is_active) {
            abort(403, 'Your account is not active.');
        }

        // If sub-agent, check if parent agent is also active
        if ($user->isSubAgent() && $user->parentAgent && ! $user->parentAgent->is_active) {
            abort(403, 'Your parent agency account is currently deactivated. Please contact your agency administrator.');
        }

        return $next($request);
    }
}
