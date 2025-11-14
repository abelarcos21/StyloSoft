<?php

namespace App\Providers;

use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Configurar URL base y HTTPS
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
            URL::forceRootUrl(config('app.url'));
        }

        // Solo configuraciones compatibles con tu versión de Laravel
        if (method_exists(Vite::class, 'prefetch')) {
            Vite::prefetch(concurrency: 3);
        }
    }
}
