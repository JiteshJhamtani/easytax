<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {

        $middleware->trustProxies(at: '*');
        
        $middleware->alias([
            'agent'   => \App\Http\Middleware\AgentMiddleware::class,
            'admin'   => \App\Http\Middleware\AdminMiddleware::class,
            'sidebar' => \App\Http\Middleware\LoadSidebarMenu::class,
            'team'    => \App\Http\Middleware\TeamMiddleware::class,
        ]);

       $middleware->redirectUsersTo(fn (\Illuminate\Http\Request $request) =>
            match (strtoupper($request->user()->role ?? 'AGENT')) {
                'ADMIN' => route('admin.dashboard'),
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