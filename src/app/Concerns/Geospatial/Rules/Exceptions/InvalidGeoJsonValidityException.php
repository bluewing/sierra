<?php

namespace Bluewing\Concerns\Geospatial\Rules\Exceptions;

use Bluewing\Concerns\Geospatial\Enumerations\GeometryType;
use Exception;

class InvalidGeoJsonValidityException extends Exception
{
    /**
     * Generates an exception that indicates the structure of the GeoJSON object is invalid.
     *
     * @return static - An instance of `InvalidGeoJsonValidityException` with an appropriate exception message.
     */
    public static function invalidStructure(): static
    {
        return new InvalidGeoJsonValidityException('The value provided to :attribute isn\'t formatted properly.');
    }

    /**
     * Generates an exception that indicates the `GeometryType`'s 'type' property does not match that of the expected
     * type.
     *
     * @param GeometryType $expectedType - The `GeometryType` that was expected.
     *
     * @return static - An instance of `InvalidGeoJsonValidityException` with an appropriate exception message.
     */
    public static function geometryTypeMismatch(GeometryType $expectedType): static
    {
        return new InvalidGeoJsonValidityException(
            "The type provided to :attribute should match '$expectedType->value'"
        );
    }

    /**
     * Generates an exception that indicates one or more of the coordinates for the GeoJSON object is out of bounds
     * (i.e. a latitude of 120°).
     *
     * @param string $coordinateType - The coordinate type that is out of bounds (either "longitude" or "latitude").
     * @param string $coordinateValue - The value of the coordinate that is out of bounds.
     *
     * @return static - An instance of `InvalidGeoJsonValidityException` with an appropriate exception message.
     */
    public static function coordinateOutOfBounds(string $coordinateType, string $coordinateValue): static
    {
        return new InvalidGeoJsonValidityException(
            "The :attribute field's $coordinateType cannot have a value of $coordinateValue."
        );
    }
}
