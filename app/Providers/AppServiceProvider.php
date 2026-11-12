<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
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
        // En production, toutes les URL générées (CinetPay, emails, redirections) sont en HTTPS,
        // même si l'hébergeur transmet la requête à PHP en HTTP derrière son proxy.
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }
}
