<?php

if (! function_exists('createModel')) {

    /**
     * Creates a model from the given string.
     *
     * @param string $model
     *
     * @return mixed - An instance of the model.
     */
    function createModel(string $model) {
        $class = '\\'.ltrim($model, '\\');

        return new $class;
    }
}
