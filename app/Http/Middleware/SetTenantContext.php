<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetTenantContext
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $host = $request->getHost();

        $tenantMapping = [
            'b2b.easytax.live' => 'b2b',
            'upwest.easytax.live' => 'upwest',
            // Add other domain mappings here
        ];

        $tenantName = $tenantMapping[$host] ?? 'default';

        config(['app.tenant' => $tenantName]);

        return $next($request);
    }
}
