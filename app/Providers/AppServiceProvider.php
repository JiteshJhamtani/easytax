<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Auth;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (app()->environment('local')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        \Illuminate\Support\Facades\View::composer('adminlte::page', function ($view) {
            if (auth()->check() && auth()->user()->isAdmin()) {
                /** @var \App\Models\User $user */
                $user = auth()->user();
                $view->with('unreadNotifications', $user->unreadNotifications()->take(5)->get());
                $view->with('unreadCount', $user->unreadNotifications()->count());
            }
        });
    }
}
