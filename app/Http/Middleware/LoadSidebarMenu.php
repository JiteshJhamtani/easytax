<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\Response;

class LoadSidebarMenu
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {

            if (auth()->user()->isAdmin()) {
                Config::set('adminlte.menu', config('menu_admin'));
            }

            if (auth()->user()->role === 'AGENT') {
                Config::set('adminlte.menu', config('menu_agent'));
            }
        }

        return $next($request);
    }
}
