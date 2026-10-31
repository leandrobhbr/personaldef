<?php

namespace App\Providers;

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
        // Em produção, links canônicos e Open Graph sempre em HTTPS
        if ($this->app->isProduction()) {
            URL::forceScheme('https');
        }

        // Link do WhatsApp com mensagem pronta: {{ $wa('Quero...') }}
        View::share('wa', fn (?string $message = null) => 'https://wa.me/'.config('site.whatsapp').'?text='.rawurlencode($message ?? config('site.whatsapp_message')));
    }
}
