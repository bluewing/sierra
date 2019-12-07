<?php


namespace Bluewing\Schema;

use Illuminate\Database\Schema\Builder;
use Illuminate\Support\Facades\Schema as BaseSchema;
use Bluewing\Schema\Blueprint as BluewingBlueprint;

/**
 * Class Schema
 *
 * @package Bluewing\Schema
 *
 * Subclass of `Schema` that returns the appropriate instance of `Blueprint` containing
 * additional utility functions.
 *
 * @see https://stackoverflow.com/questions/22444685/extend-blueprint-class/57539154#57539154
 */
class Schema extends BaseSchema
{

    /**
     * Get a schema builder instance for a connection.
     *
     * @param  string|null  $name
     * @return Builder
     */
    public static function connection($name): Builder
    {
        /** @var Builder $builder */
        $builder = static::$app['db']->connection($name)->getSchemaBuilder();
        $builder->blueprintResolver(static function($table, $callback) {
            return new BluewingBlueprint($table, $callback);
        });
        return $builder;
    }

    /**
     * Get a schema builder instance for the default connection.
     *
     * @return Builder
     */
    protected static function getFacadeAccessor(): Builder
    {
        /** @var Builder $builder */
        $builder = static::$app['db']->connection()->getSchemaBuilder();
        $builder->blueprintResolver(static function($table, $callback) {
            return new BluewingBlueprint($table, $callback);
        });
        return $builder;
    }
}
