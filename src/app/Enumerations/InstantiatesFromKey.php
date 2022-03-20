<?php

namespace Bluewing\Enumerations;

use ReflectionEnum;
use ReflectionException;

trait InstantiatesFromKey
{
    /**
     * Static helper function that allows an enumeration to be instantiated from its key, via the `ReflectionEnum` class.
     * For example, a `Foo::Blah` enumeration case could be instantiated by calling this method like so:
     * `Foo::fromKey('Blah')`.
     *
     * @param string $key The key that represents the enum.
     *
     * @return static The instantiated enumeration this trait is present on.
     *
     * @throws ReflectionException
     */
    public static function fromKey(string $key): static
    {
        return (new ReflectionEnum(static::class))->getCase($key)->getValue();
    }
}
