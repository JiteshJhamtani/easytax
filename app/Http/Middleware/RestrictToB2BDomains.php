<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RestrictToB2BDomains
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Do not block anything on local development environments
        if (app()->environment('local')) {
            return $next($request);
        }

        $allowedDomains = [
            'uat.easytax.live',
            'b2b.easytax.live',
        ];

        if (!in_array($request->getHost(), $allowedDomains)) {
            abort(403, 'This feature is not available on this server environment.');
        }

        return $next($request);
    }
}
