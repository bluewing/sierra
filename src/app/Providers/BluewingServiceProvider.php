<?php

namespace Bluewing\Providers;

use Bluewing\Database\PostgresConnection as BluewingPostgresConnection;
use Bluewing\Schema\Schema;
use Illuminate\Database\Connection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class BluewingServiceProvider extends ServiceProvider {

    /**
     * @return void
     */
    public function register()
    {
        $this->app->bind('db.schema', fn() => Schema::customizedSchemaBuilder());
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

    /**
     * Registers a custom `BluewingPostgresConnection` class for the 'pgsql' connection. This class provides access to
     * a custom `PostgresBuilder` class, which itself provides enhancements to schema changes and migrations.
     *
     * Note this is not currently used, but is provided here as reference.
     */
    private function registerCustomConnectionResolver()
    {
        Connection::resolverFor('pgsql', function ($connection, $database, $prefix, $config) {
            return new BluewingPostgresConnection($connection, $database, $prefix, $config);
        });
    }
}
