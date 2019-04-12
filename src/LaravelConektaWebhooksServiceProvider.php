<?php

namespace Yorchi\LaravelConektaWebhooks;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class LaravelConektaWebhooksServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/config.php' => config_path('conekta-webhooks.php'),
            ], 'config');
        }

        Route::macro('conektaWebhooks', function ($url) {
            return Route::post($url, '\Yorchi\LaravelConektaWebhooks\ConektaWebhooksController');
        });
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'conekta-webhooks');
    }
}
