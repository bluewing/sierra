<?php

namespace Bluewing\Providers;

use Bluewing\Auth\RefreshTokenManager;
use Bluewing\Services\TokenGenerator;
use Illuminate\Support\ServiceProvider;
use Bluewing\Auth\JwtManager;

class BluewingServiceProvider extends ServiceProvider {

    /**
     * Registers an instance of `JwtManager` and `RefreshTokenManager`, with the application.
     */
    public function register() {
        $this->app->bind('Bluewing\Auth\JwtManager', function($app) {
            return new JwtManager(config('app.name'), config('app.key'));
        });

        $this->app->bind('Bluewing\Auth\RefreshTokenManager', function($app) {
            return new RefreshTokenManager(new TokenGenerator(), config('bluewing.refreshtokens.model'));
        });
    }
}
