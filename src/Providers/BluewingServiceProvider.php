<?php

namespace Bluewing\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class BluewingServiceProvider extends ServiceProvider {

    /**
     * @return void
     */
    public function register()
    {

    }

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config.php' => config_path('bluewing.php')
        ]);

        Auth::provider('bluewing', function($app, array $config) {
            return new BluewingUserProvider($app['hash'], $config['model']);
        });
    }
}
