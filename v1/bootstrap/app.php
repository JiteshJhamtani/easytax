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
        $middleware->alias([
            'agent'   => \App\Http\Middleware\AgentMiddleware::class,
            'admin'   => \App\Http\Middleware\AdminMiddleware::class,
            'sidebar' => \App\Http\Middleware\LoadSidebarMenu::class,
        ]);

        $middleware->redirectUsersTo(fn (\Illuminate\Http\Request $request) =>
            $request->user()->role === 'ADMIN' ? route('admin.dashboard') : route('agent.dashboard')
        );

        $middleware->validateCsrfTokens(except: [
            'payment/webhook',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
