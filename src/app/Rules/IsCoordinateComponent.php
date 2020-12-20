<?php

namespace Bluewing\Rules;


abstract class IsCoordinateComponent
{
    /**
     * @param $value - The value to be checked for whether it's a coordinate component.
     * @param $rangeInDegrees - The acceptable range in degrees that's allowed for the value.
     *
     * @return bool - `true` if the value appears to be a coordinate component, `false` otherwise.
     */
    public function isCoordinate($value, int $rangeInDegrees): bool
    {
        return is_numeric($value)
            && $value >= $rangeInDegrees * -1
            && $value <= $rangeInDegrees
            && preg_match('/^-?\d{1,' . strlen($rangeInDegrees) . '}\.\d{0,6}$/', (string) $value) === 1;
    }
}
