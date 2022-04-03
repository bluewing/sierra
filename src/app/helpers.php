<?php

use Bluewing\Eloquent\Model;

if (! function_exists('createModel')) {

    /**
     * Creates a model from the given string.
     *
     * @param string $model
     *
     * @return Model - An instance of the model.
     */
    function createModel(string $model): Model
    {
        $class = '\\' . ltrim($model, '\\');

        return new $class;
    }
}

if (!function_exists('getShortName')) {

    /**
     * Retrieves the short class name for the provided class string. This removes any namespacing information. For
     * example: passing a class `Bluewing\Eloquent\Model` will return `Model`. This is accomplished by instantiating
     * a `ReflectionClass` instance for the provided class.
     *
     * @return string - The short class name for the fully-qualified name
     *
     * @throws ReflectionException - As this function uses the Reflection API, a `ReflectionException` is always possible.
     */
    function getShortName(string $class): string
    {
        return (new ReflectionClass($class))->getShortName();
    }
}
