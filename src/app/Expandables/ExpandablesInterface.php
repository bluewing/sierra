<?php

namespace Bluewing\Expandables;

/**
 * An interface that defines the guaranteed methods present on an `Expandables` class.
 *
 * @package Bluewing\Expandables
 */
interface ExpandablesInterface
{
    public function always(): array;
}