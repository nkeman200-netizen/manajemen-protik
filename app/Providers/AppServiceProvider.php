<?php

namespace App\Providers;

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
        \App\Models\Finance::observe(\App\Observers\AuditObserver::class);
        \App\Models\Document::observe(\App\Observers\AuditObserver::class);
        \App\Models\Agenda::observe(\App\Observers\AuditObserver::class);
        \App\Models\Warning::observe(\App\Observers\AuditObserver::class);
        \App\Models\Event::observe(\App\Observers\AuditObserver::class);
        \App\Models\User::observe(\App\Observers\AuditObserver::class);
    }
}
