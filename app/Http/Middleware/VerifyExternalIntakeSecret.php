<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyExternalIntakeSecret
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $incomingSecret = $request->header('X-EasyTax-Secret') ?? $request->bearerToken();
        $configuredSecret = config('services.easytax.external_secret');

        if (! $configuredSecret || ! $incomingSecret || ! hash_equals((string) $configuredSecret, (string) $incomingSecret)) {
            return response()->json([
                'ok' => false,
                'error' => 'Unauthorized. Invalid or missing X-EasyTax-Secret header.',
            ], 401);
        }

        return $next($request);
    }
}
