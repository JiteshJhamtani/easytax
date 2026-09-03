<?php

namespace App\Providers;

use App\Models\User;
use App\View\Composers\AdminSidebarComposer;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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
        // 1. Force HTTPS on Production/UAT
        if (config('app.env') !== 'local') {
            URL::forceScheme('https');
        }

        // 2. Your existing notifications logic
        View::composer('adminlte::page', function ($view) {
            if (auth()->check() && auth()->user()->isAdmin()) {
                /** @var User $user */
                $user = auth()->user();
                $view->with('unreadNotifications', $user->unreadNotifications()->take(5)->get());
                $view->with('unreadCount', $user->unreadNotifications()->count());
            }
        });

        // Dynamic sidebar tab count badges
        View::composer('layouts.admin', AdminSidebarComposer::class);

        // 3. Custom EasyTax Password Reset Email Template
        ResetPassword::toMailUsing(function (object $notifiable, string $token) {
            return (new MailMessage)
                ->subject('Reset Your EasyTax Agent Password')
                ->greeting('Hello Agent,')
                ->line('We received a request to reset the password for your EasyTax account.')
                ->action('Reset My Password', url(route('password.reset', [
                    'token' => $token,
                    'email' => $notifiable->getEmailForPasswordReset(),
                ], false)))
                ->line('If you did not request this, please ignore this email. Your account is safe.');
        });

        // 4. Implicitly grant "admin" role all permissions
        Gate::before(function ($user, $ability) {
            return $user->hasRole('admin') ? true : null;
        });

        // 5. Custom @tenant Blade directive
        Blade::if('tenant', function ($tenant) {
            return config('app.tenant') === $tenant;
        });

        // 6. Custom @b2bOnly Blade directive
        Blade::if('b2bOnly', function () {
            if (app()->environment('local')) {
                return true;
            }

            return in_array(request()->getHost(), ['uat.easytax.live', 'b2b.easytax.live']);
        });
    }
}
