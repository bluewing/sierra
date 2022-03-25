<?php

namespace Bluewing\Rules;


abstract class IsCoordinateComponent
{
    /**
     * Validates that the coordinate component is of the correct type (either an int/float) and is in the correct range
     * for the allowed coordinate type (i.e. -180 to 180 for longitudes, -90 to 90 for latitudes).
     *
     * @param mixed $value - The value to be checked for whether it's a coordinate component. This is declared to be of
     * type `mixed`, but is validated as `int`/`float` inside the method.
     * @param int $rangeInDegrees - The acceptable range in degrees that's allowed for the value.
     *
     * @return bool - `true` if the value appears to be a coordinate component, `false` otherwise.
     */
    public function isCoordinate(mixed $value, int $rangeInDegrees): bool
    {
        return (is_int($value) || is_float($value))
            && $value >= $rangeInDegrees * -1
            && $value <= $rangeInDegrees;
    }
}
