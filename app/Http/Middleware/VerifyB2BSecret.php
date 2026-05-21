    public function handle($request, \Closure $next)
    {
        // Check if the incoming request has the correct Bearer Token
        $token = (string) $request->bearerToken();
        $secret = config('b2b.sync_secret', '');

        if (!$token || !hash_equals($secret, $token)) {
            return response()->json([
                'success' => false,
                'error' => 'Unauthorized Access. Invalid B2B Token.'
            ], 401);
        }

        return $next($request);
    }