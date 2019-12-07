<?php

namespace Bluewing\Schema;

use Illuminate\Database\Schema\ColumnDefinition;

/**
 * Trait PrefersCamelCaseColumnNames
 *
 * @package Bluewing\Schema
 *
 * Replaces all the hardcoded pascal_case column definitions in the `Blueprint` schema management class with CamelCase
 * equivalents.
 */
trait PrefersCamelCaseColumnNames
{

    /**
     * The name for columns supporting the recording of when a model was created.
     */
    private $createdAtColumnName = 'createdAt';

    /**
     * The name for columns supporting the recording of when a model was updated.
     */
    private $updatedAtColumnName = 'updatedAt';

    /**
     * The name for columns supporting the recording of when a model was deleted.
     */
    private $deletedAtColumnName = 'deletedAt';

    /**
     * The name for columns that store a rememberToken.
     */
    private $rememberTokenColumnName = 'rememberToken';

    /**
     * Indicate that the timestamp columns should be dropped.
     *
     * @return void
     */
    public function dropTimestamps()
    {
        $this->dropColumn($this->createdAtColumnName, $this->updatedAtColumnName);
    }

    /**
     * Indicate that the soft delete column should be dropped.
     *
     * @param  string  $column
     * @return void
     */
    public function dropSoftDeletes($column = 'deletedAt')
    {
        $this->dropColumn($column);
    }

    /**
     * Indicate that the soft delete column should be dropped.
     *
     * @param  string  $column
     * @return void
     */
    public function dropSoftDeletesTz($column = 'deletedAt')
    {
        $this->dropSoftDeletes($column);
    }

    /**
     * Indicate that the remember token column should be dropped.
     *
     * @return void
     */
    public function dropRememberToken()
    {
        $this->dropColumn($this->rememberTokenColumnName);
    }

    /**
     * Indicate that the polymorphic columns should be dropped.
     *
     * @param  string  $name
     * @param  string|null  $indexName
     * @return void
     */
    public function dropMorphs($name, $indexName = null)
    {
        $this->dropIndex($indexName ?: $this->createIndexName('index', ["{$name}Type", "{$name}_id"]));

        $this->dropColumn("{$name}Type", "{$name}Id");
    }

    /**
     * Add nullable creation and update timestamps to the table.
     *
     * @param  int  $precision
     * @return void
     */
    public function timestamps($precision = 0)
    {
        $this->timestamp($this->createdAtColumnName, $precision)->nullable();

        $this->timestamp($this->updatedAtColumnName, $precision)->nullable();
    }

    /**
     * Add creation and update timestampTz columns to the table.
     *
     * @param  int  $precision
     * @return void
     */
    public function timestampsTz($precision = 0)
    {
        $this->timestampTz($this->createdAtColumnName, $precision)->nullable();

        $this->timestampTz($this->updatedAtColumnName, $precision)->nullable();
    }

    /**
     * Add a "deleted at" timestamp for the table.
     *
     * @param  string  $column
     * @param  int  $precision
     * @return ColumnDefinition
     */
    public function softDeletes($column = 'deletedAt', $precision = 0)
    {
        return $this->timestamp($column, $precision)->nullable();
    }

    /**
     * Add a "deleted at" timestampTz for the table.
     *
     * @param  string  $column
     * @param  int  $precision
     * @return ColumnDefinition
     */
    public function softDeletesTz($column = 'deletedAt', $precision = 0)
    {
        return $this->timestampTz($column, $precision)->nullable();
    }

    /**
     * Add the proper columns for a polymorphic table.
     *
     * @param  string  $name
     * @param  string|null  $indexName
     * @return void
     */
    public function morphs($name, $indexName = null)
    {
        $this->string("{$name}Type");

        $this->unsignedBigInteger("{$name}Id");

        $this->index(["{$name}Type", "{$name}Id"], $indexName);
    }

    /**
     * Add nullable columns for a polymorphic table.
     *
     * @param  string  $name
     * @param  string|null  $indexName
     * @return void
     */
    public function nullableMorphs($name, $indexName = null)
    {
        $this->string("{$name}Type")->nullable();

        $this->unsignedBigInteger("{$name}Id")->nullable();

        $this->index(["{$name}Type", "{$name}Id"], $indexName);
    }

    /**
     * Add the proper columns for a polymorphic table using UUIDs.
     *
     * @param  string  $name
     * @param  string|null  $indexName
     * @return void
     */
    public function uuidMorphs($name, $indexName = null)
    {
        $this->string("{$name}Type");

        $this->uuid("{$name}Id");

        $this->index(["{$name}Type", "{$name}Id"], $indexName);
    }

    /**
     * Add nullable columns for a polymorphic table using UUIDs.
     *
     * @param  string  $name
     * @param  string|null  $indexName
     * @return void
     */
    public function nullableUuidMorphs($name, $indexName = null)
    {
        $this->string("{$name}Type")->nullable();

        $this->uuid("{$name}Id")->nullable();

        $this->index(["{$name}Type", "{$name}Id"], $indexName);
    }

    /**
     * Adds the `remember_token` column to the table.
     *
     * @return ColumnDefinition
     */
    public function rememberToken()
    {
        return $this->string($this->rememberTokenColumnName, 100)->nullable();
    }
}
