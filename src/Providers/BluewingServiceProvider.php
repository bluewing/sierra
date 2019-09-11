<?php

namespace Bluewing\SharedServer\Providers;

use Illuminate\Support\ServiceProvider;
use Bluewing\SharedServer\Jwt;

class BluewingServiceProvider extends ServiceProvider {

    /**
     * Registers an instance of `JwtManager` with the application.
     */
    public function register() {
        $this->app->bind('Bluewing\SharedServer\Managers\JwtManager', function($app) {
            return new JwtManager(config('app.name'), config('app.key'));
        });
    }
}