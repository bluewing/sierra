<?php

namespace Bluewing\Eloquent;

/**
 *
 */
trait UsesCamelCaseAttributes
{
    /**
     * Do not use snake_case for model attributes.
     */
    public static $snakeAttributes = false;
}