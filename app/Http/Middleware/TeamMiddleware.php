<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeamMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Check if user is logged in AND their role is 'team' (or admins)
        $allowedRoles = ['team', 'admin', 'sub-admin', 'super-admin', 'super_admin'];
        if (Auth::check() && in_array(strtolower(Auth::user()->role), $allowedRoles)) {
            return $next($request);
        }

        // If they are an agent or guest, kick them back to the homepage
        return redirect('/')->with('error', 'Unauthorized access. Internal Team only.');
    }
}
