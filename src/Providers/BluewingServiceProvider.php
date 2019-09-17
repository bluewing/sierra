<?php

namespace Bluewing\Providers;

use Illuminate\Support\ServiceProvider;
use Bluewing\Jwt\JwtManager;

class BluewingServiceProvider extends ServiceProvider {

    /**
     * Registers an instance of `JwtManager` with the application.
     */
    public function register() {
        $this->app->bind('Bluewing\Jwt\JwtManager', function($app) {
            return new JwtManager(config('app.name'), config('app.key'));
        });
    }
}