<?php

namespace Bluewing\Rules;

use Illuminate\Contracts\Validation\Rule;

class DateAfter extends AbstractDateComparison implements Rule
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
        return $this->validateDates($value, function($dateBeingValidated, $dateToCheck) {
            $dateBeingValidated->lessThanOrEqualTo($dateToCheck);
        });
    }

    /**
     * Get the validation error message.
     *
     * @return string - The validation error message.
     */
    public function message()
    {
        $dateString = implode(', ', $this->dates);
        return "The :attribute date :input is not after these dates: {$dateString}";
    }
}
