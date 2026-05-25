<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;
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
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        // 2. Your existing notifications logic
        \Illuminate\Support\Facades\View::composer('adminlte::page', function ($view) {
            if (auth()->check() && auth()->user()->isAdmin()) {
                /** @var \App\Models\User $user */
                $user = auth()->user();
                $view->with('unreadNotifications', $user->unreadNotifications()->take(5)->get());
                $view->with('unreadCount', $user->unreadNotifications()->count());
            }
        });

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
        \Illuminate\Support\Facades\Gate::before(function ($user, $ability) {
            return $user->hasRole('admin') ? true : null;
        });

        // 5. Custom @tenant Blade directive
        \Illuminate\Support\Facades\Blade::if('tenant', function ($tenant) {
            return config('app.tenant') === $tenant;
        });

        // 6. Custom @b2bOnly Blade directive
        \Illuminate\Support\Facades\Blade::if('b2bOnly', function () {
            if (app()->environment('local')) {
                return true;
            }
            return in_array(request()->getHost(), ['uat.easytax.live', 'b2b.easytax.live']);
        });
    }
}
