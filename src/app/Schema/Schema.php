<?php


namespace Bluewing\Schema;

use Illuminate\Database\Schema\Builder;
use Illuminate\Support\Facades\Schema as BaseSchema;
use Bluewing\Schema\Blueprint as BluewingBlueprint;

/**
 * @package Bluewing\Schema
 *
 * Subclass of `Schema` that returns the appropriate instance of `Blueprint` containing additional utility functions.
 *
 * @see https://stackoverflow.com/questions/22444685/extend-blueprint-class/57539154#57539154
 */
class Schema extends BaseSchema
{
    /**
     * Get a schema builder instance for a connection.
     *
     * @param string|null $name
     *
     * @return Builder
     */
    public static function connection($name): Builder
    {
        return static::customizedSchemaBuilder($name);
    }

    /**
     * Retrieves an instance of the schema `Builder` with a customized `Blueprint` class.
     *
     * @param string|null $name
     *
     * @return Builder
     */
    public static function customizedSchemaBuilder(string|null $name = null): Builder
    {
        /** @var Builder $builder */
        $builder = static::$app['db']->connection($name)->getSchemaBuilder();
        $builder->blueprintResolver(static fn($table, $callback) => new BluewingBlueprint($table, $callback));
        return $builder;
    }
}
