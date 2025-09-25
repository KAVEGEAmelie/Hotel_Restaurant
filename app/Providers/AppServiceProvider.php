<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;

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
        // Gérer les erreurs de connexion à la base de données
        DB::listen(function ($query) {
            if ($query->time > 1000) {
                Log::warning('Requête lente détectée', [
                    'sql' => $query->sql,
                    'time' => $query->time
                ]);
            }
        });

        // Forcer HTTPS en production
        if (config('app.env') === 'production') {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }
    }
}
