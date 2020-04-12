<?php

namespace Bluewing\Providers;

use Bluewing\Auth\Services\JwtManager;
use Bluewing\Guards\JwtGuard;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class JsonWebTokenServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function register()
    {
        $this->app->bind('Bluewing\Auth\JwtManager', function($app) {
            return new JwtManager(config('app.name'), config('app.key'));
        });
    }

    /**
     * return void
     */
    public function boot()
    {
        Auth::extend('jwt', function ($app, $name, array $config) {
            return new JwtGuard(Auth::createUserProvider($config['provider']), $app->make('request'));
        });
    }
}
