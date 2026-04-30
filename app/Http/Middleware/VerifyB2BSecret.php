public function handle($request, \Closure $next)
    {
        // Check if the incoming request has the correct Bearer Token
        $token = $request->bearerToken();

        if ($token !== env('B2B_SYNC_SECRET')) {
            return response()->json([
                'success' => false,
                'error' => 'Unauthorized Access. Invalid B2B Token.'
            ], 401);
        }

        return $next($request);
    }