<?php


namespace Bluewing\Auth\Passwords;

use Illuminate\Auth\Passwords\PasswordResetServiceProvider;

class BluewingPasswordResetServiceProvider extends PasswordResetServiceProvider
{
    /**
     * Register the password broker instance.
     *
     * @return void
     */
    protected function registerPasswordBroker()
    {
        $this->app->singleton('auth.password', fn($app) => new BluewingPasswordBrokerManager($app));
        
        $this->app->bind('auth.password.broker', fn($app) => $app->make('auth.password')->broker());
    }
}
