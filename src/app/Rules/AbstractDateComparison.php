<?php

namespace Bluewing\Rules;

use Carbon\Carbon;

abstract class AbstractDateComparison
{
    /**
     * The array of dates that are being validated.
     *
     * @var array
     */
    protected array $dates;

    /**
     * The provided field under validation must be a date that is before/after any of the dates provided in the
     * constructor. The dates provided to the constructor are parsed to instances of `Carbon`, if they are not already
     *
     * @param array $dates - The dates that the attribute value must be before for validation purposes.
     */
    public function __construct(...$dates)
    {
        $this->dates = array_map(fn($date) => $date instanceof Carbon ? $date : Carbon::parse($date), $dates);
    }

    /**
     * Validate the date for the rule with the given validation function.
     *
     * @param $value - The date being validated.
     * @param $validationFn - The validation function being run against the date.
     *
     * @return bool - `true` if the date passes the validation function for the given dates, `false` otherwise.
     */
    protected function validateDates($value, $validationFn): bool
    {
        $dateBeingValidated = Carbon::parse($value);

        if (is_callable($validationFn)) {
            foreach ($this->dates as $dateToCheck) {
                if ($validationFn($dateBeingValidated, $dateToCheck)) {
                    return false;
                }
            }
            return true;
        }
        return false;
    }
}
