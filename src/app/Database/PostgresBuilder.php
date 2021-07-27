<?php


namespace Bluewing\Database;

use Bluewing\Schema\Blueprint;
use Bluewing\Schema\NoModelCommentException;
use Closure;
use Illuminate\Database\Schema\PostgresBuilder as BasePostgresBuilder;
use ReflectionClass;
use ReflectionException;

class PostgresBuilder extends BasePostgresBuilder
{
    /**
     * Create a new table on the schema.
     *
     * @param string $classOrTableName
     * @param Closure $callback
     *
     * @return void
     */
    public function create($classOrTableName, Closure $callback)
    {
        $tableName = $this->resolveTableNameFromEloquentModel($classOrTableName);

        $this->build(tap($this->createBlueprint($tableName), function ($blueprint) use ($callback, $classOrTableName) {
            $blueprint->create();

            $callback($blueprint);

            if ($blueprint instanceof Blueprint) {
                try {
                    $blueprint->addModelComments($classOrTableName);
                } catch (NoModelCommentException|ReflectionException) {};
            }
        }));
    }

    /**
     * @param string $classOrTableName -
     *
     * @return void
     */
    public function dropIfExists($classOrTableName)
    {
        parent::dropIfExists($this->resolveTableNameFromEloquentModel($classOrTableName));
    }

    /**
     * Resolves a table name from the provided $classOrTableName argument. If the argument resolves to a class, and
     * that class contains a property called `table`, the value of that property is returned by this method. If
     * either the argument does not represent a class name, or the `table` property does not exist, the argument is
     * returned unmodified.
     *
     * @param string $classOrTableName - A string representing the class, or the name of the table to use.
     *
     * @return string - The name of the table to use.
     */
    private function resolveTableNameFromEloquentModel(string $classOrTableName): string
    {
        try {

            $reflection         = new ReflectionClass($classOrTableName);
            $reflectionProperty = $reflection->getProperty('table');
            $reflectionProperty->setAccessible(true);

            $obj = createModel($classOrTableName);
            return $reflectionProperty->getValue($obj);

        } catch (ReflectionException $e) {
            return $classOrTableName;
        }
    }
}