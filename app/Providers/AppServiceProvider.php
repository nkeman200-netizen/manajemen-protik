<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Dedoc\Scramble\Scramble;
use Illuminate\Routing\Route;
use Illuminate\Support\Str;


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
        \App\Models\Finance::observe(\App\Observers\AuditObserver::class);
        \App\Models\Document::observe(\App\Observers\AuditObserver::class);
        \App\Models\Agenda::observe(\App\Observers\AuditObserver::class);
        \App\Models\Warning::observe(\App\Observers\AuditObserver::class);
        \App\Models\Event::observe(\App\Observers\AuditObserver::class);
        \App\Models\User::observe(\App\Observers\AuditObserver::class);
        // Mendaftarkan agar Scramble hanya membaca route dengan prefix 'api/'
        Scramble::routes(function (Route $route) {
            return Str::startsWith($route->uri, 'api/');
        });

        // Membatasi akses ke halaman dokumentasi API di mode Production
        Gate::define('viewApiDocs', function ($user) {
            return in_array($user->email, ['sofyan@protic.com']);
        });
    }
}
