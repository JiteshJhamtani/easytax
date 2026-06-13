<?php

use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\AgentMiddleware;
use App\Http\Middleware\LoadSidebarMenu;
use App\Http\Middleware\MarketerMiddleware;
use App\Http\Middleware\RestrictToB2BDomains;
use App\Http\Middleware\SetTenantContext;
use App\Http\Middleware\TeamMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {

        $middleware->trustProxies(at: '*');
        $middleware->append(SetTenantContext::class);

        $middleware->alias([
            'agent' => AgentMiddleware::class,
            'admin' => AdminMiddleware::class,
            'sidebar' => LoadSidebarMenu::class,
            'team' => TeamMiddleware::class,
            'marketer' => MarketerMiddleware::class,
            'b2b.only' => RestrictToB2BDomains::class,
        ]);

        $middleware->redirectUsersTo(fn (Request $request) => match (strtoupper($request->user()->role ?? 'AGENT')) {
            'ADMIN', 'SUPER_ADMIN', 'SUB-ADMIN' => route('admin.dashboard'),
            'TEAM' => route('team.dashboard'),
            'MARKETER' => route('marketer.dashboard'), // Fixes your marketers too!
            default => route('agent.dashboard'), // Agents and anyone else
        }
        );

        $middleware->validateCsrfTokens(except: [
            'payment/webhook',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
