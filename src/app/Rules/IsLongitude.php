<?php

namespace Bluewing\Rules;

use Illuminate\Contracts\Validation\Rule;

class IsLongitude extends IsCoordinateComponent implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param $attribute - The attribute that is being checked.
     * @param $value - The value that is being validated.
     *
     * @return bool - `true` if the validation rule passed successfully, `false` otherwise.
     */
    public function passes($attribute, $value)
    {
        return $this->isCoordinate($value, 180);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'That is not a valid longitude';
    }
}
