<?php


namespace Bluewing\Providers;


use Bluewing\Auth\Services\RefreshTokenManager;
use Bluewing\Services\TokenGenerator;
use Illuminate\Support\ServiceProvider;

class RefreshTokenServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function register()
    {
        $this->app->bind('Bluewing\Auth\RefreshTokenManager', function($app) {
            return new RefreshTokenManager(
                new TokenGenerator(),
                createModel(config('bluewing.refreshTokens.model'))
            );
        });
    }

    /**
     * @return void
     */
    public function boot()
    {

    }
}
