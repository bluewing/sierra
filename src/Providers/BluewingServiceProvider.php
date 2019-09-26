<?php

namespace Bluewing\Providers;

use Bluewing\Auth\RefreshTokenManager;
use Bluewing\Guards\JwtGuard;
use Bluewing\Services\TokenGenerator;
use Illuminate\Support\Facades\Auth;
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

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot() {
        $this->publishes([
            __DIR__ . '/../config.php' => config_path('bluewing.php')
        ]);
        
        Auth::provider('bluewing', function($app, array $config) {
            return new BluewingUserProvider($app['hash'], $config['model']);
        });

        Auth::extend('jwt', function ($app, $name, array $config) {
            return new JwtGuard(Auth::createUserProvider($config['provider']), $app->make('request'));
        });
    }
}
