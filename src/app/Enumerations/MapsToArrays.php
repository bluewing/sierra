<?php

namespace Bluewing\Enumerations;

trait MapsToArrays
{
    /**
     * Helper function that maps the cases of an enumeration to their backed values, returning an `array`.
     *
     * @return array An array of backed values in the enumeration.
     */
    public static function asBackedValueArray(): array
    {
        return array_map(fn($case) => $case->value, static::cases());
    }

    /**
     * Helper function that maps the cases of an enumeration to their keys, returning an `array`.
     *
     * @return array An array of keys in the enumeration.
     */
    public static function asKeyArray(): array
    {
        return array_map(fn($case) => $case->name, static::cases());
    }

    /**
     * Helper function that maps the cases of an enumeration to the backed values, keyed by the enumeration keys.
     *
     * @return array An associative array of enumeration keys for backed values.
     */
    public static function asBackedValueAssociativeArray(): array
    {
        return array_combine(static::asKeyArray(), static::asBackedValueArray());
    }
}
