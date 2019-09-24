<?php

namespace Bluewing\Providers;

use Illuminate\Support\ServiceProvider;
use Bluewing\Auth\JwtManager;

class BluewingServiceProvider extends ServiceProvider {

    /**
     * Registers an instance of `JwtManager` with the application.
     */
    public function register() {
        $this->app->bind('Bluewing\Auth\JwtManager', function($app) {
            return new JwtManager(config('app.name'), config('app.key'));
        });
    }

    public function boot() {
        $this->publishes(
            __DIR__.'../'
        );
    }
}
