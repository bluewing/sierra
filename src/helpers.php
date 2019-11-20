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
        $class = '\\'.ltrim($model, '\\');

        return new $class;
    }
}
