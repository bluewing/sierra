<?php

namespace Bluewing\Concerns\Geospatial\Rules;

use Bluewing\Concerns\Geospatial\Enumerations\GeometryType;
use Bluewing\Concerns\Geospatial\Rules\Exceptions\InvalidGeoJsonValidityException;
use Bluewing\Rules\IsLatitude;
use Bluewing\Rules\IsLongitude;
use Illuminate\Contracts\Validation\Rule;

class IsGeoJson implements Rule
{
    /**
     * @var InvalidGeoJsonValidityException|null - The generated exception if the GeoJSON validation fails.
     */
    protected ?InvalidGeoJsonValidityException $exception;

    /**
     * `IsGeoJson` provides support for validating the schema and structure of GeoJSON data before it is handled
     * by the application.
     *
     * @param GeometryType $type - The enumeration indicating what type the `GeoJSON` document refers to.
     */
    public function __construct(protected GeometryType $type) {}

    /**
     * Static helper function which returns an instance of this class, allowing data structures
     * to be validated as specific GeoJSON geometry types.
     *
     * @param GeometryType $type - The enumeration indicating what type the `GeoJSON` document refers to.
     *
     * @return static - An instance of the `IsGeoJson` rule.
     */
    public static function ofType(GeometryType $type): static
    {
        return new IsGeoJson($type);
    }

    /**
     * Checks if the GeoJSON value is valid for the attribute.
     *
     * @param $attribute - The name of the attribute under validation.
     * @param $value - The value of the attribute being validated as GeoJSON.
     *
     * @return bool - `true` if the value is valid GeoJSON, `false` otherwise.
     */
    public function passes($attribute, $value)
    {
        try {
            $this->validateStructure($value);
            $this->validateCoordinates($value);
            return true;

        } catch (InvalidGeoJsonValidityException $e) {
            $this->exception = $e;
            return false;
        }
    }

    /**
     * Gets the validation error message.
     *
     * @return string - The `message` property stored on the `InvalidGeoJsonValidityException` that was thrown due to
     * a validation failure.
     */
    public function message(): string
    {
        return $this->exception?->getMessage() ?? '';
    }

    /**
     * Validates that the structure of the content matches what should be found for the expected `GeometryType`. This
     * includes checking the `type` property matches the expected `GeometryType`, and that the `coordinates` property
     * is present on the object.
     *
     * @param mixed $value - The value of the attribute being validated as GeoJSON.
     *
     * @throws InvalidGeoJsonValidityException - An `InvalidGeoJsonValidityException` will be thrown if the structure
     * being validated does not satisfy expectations.
     */
    private function validateStructure(mixed $value): void
    {
        if (!is_array($value) || !array_key_exists('type', $value) || !array_key_exists('coordinates', $value)) {
            throw InvalidGeoJsonValidityException::invalidStructure();

        } else if ($value['type'] !== $this->type->value) {
            throw InvalidGeoJsonValidityException::geometryTypeMismatch($this->type);
        }
    }

    /**
     * Validates that the coordinates structure of the GeoJSON object is correct for the `GeometryType`. For example, a
     * point should have a `coordinates` property containing a single position.
     *
     * @param array $value - The value of the attribute being validated as GeoJSON.
     *
     * @throws InvalidGeoJsonValidityException - An `InvalidGeoJsonValidityException` will be thrown if the coordinates
     * provided do not satisfy expectations.
     */
    private function validateCoordinates(array $value): void
    {
        // Ensure that the coordinates property is an `array`.
        if (!is_array($value['coordinates'])) {
            throw InvalidGeoJsonValidityException::invalidStructure();
        }

        switch ($this->type) {
            case GeometryType::Point:
                $this->validatePosition($value['coordinates']);
                break;
            default:
                break;
        }
    }

    /**
     * Validates the contents of a GeoJSON position by checking it contains the expected number of members, and both
     * values are valid as longitude and latitude, respectively. Note this method does not support elevation data yet.
     *
     * @param array $position - The position tuple of the GeoJSON coordinate, stored as an `array`.
     *
     * @throws InvalidGeoJsonValidityException - An `InvalidGeoJsonValidityException` will be thrown if the coordinates
     * provided do not satisfy expectations.
     */
    private function validatePosition(array $position): void
    {
        // Check each coordinate only contains two values
        if (count($position) !== 2) {
            throw InvalidGeoJsonValidityException::invalidStructure();
        }

        // Check each value is a valid longitude/latitude
        [$long, $lat] = $position;
        if (!(new IsLongitude)->passes('', $long)) {
            throw InvalidGeoJsonValidityException::coordinateOutOfBounds('longitude', $long);
        }

        if (!(new IsLatitude)->passes('', $lat)) {
            throw InvalidGeoJsonValidityException::coordinateOutOfBounds('latitude', $lat);
        }
    }
}
