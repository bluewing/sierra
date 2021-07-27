<?php


namespace Bluewing\Database;

use Illuminate\Database\PostgresConnection as BasePostgresConnection;

class PostgresConnection extends BasePostgresConnection
{
    /**
     * Get a schema builder instance for the connection. This overrides the default `PostgresConnection` class by
     * returning a customised `PostgresBuilder` which has modified functionality to directly grab the table name for
     * a class from a provided Eloquent model.
     *
     * @return PostgresBuilder - An instance of the modified `PostgresBuilder` to use.
     */
    public function getSchemaBuilder(): PostgresBuilder
    {
        if (is_null($this->schemaGrammar)) {
            $this->useDefaultSchemaGrammar();
        }
        return new PostgresBuilder($this);
    }
}