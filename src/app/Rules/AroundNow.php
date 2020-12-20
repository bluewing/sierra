<?php

namespace Bluewing\Rules;

use Carbon\Carbon;
use Illuminate\Contracts\Validation\Rule;
use InvalidArgumentException;

class AroundNow implements Rule
{
    /**
     * The delta around the current instant of time that is acceptable.
     *
     * @var int
     */
    protected int $delta;

    /**
     * Customizes the delta that should be used if the date is ahead of now.
     *
     * @var int|null
     */
    protected ?int $forwardDelta;

    /**
     * Create a new rule instance.
     *
     * @param int $delta - The duration in seconds that should be allowed as a delta on either side of the attribute
     * value.
     * @param int|null $forwardDelta - An optional duration in seconds that should be allowed as a delta on the forward
     * side of the attribute value. This defaults to null, and if not provided, the $delta parameter is used instead.
     */
    public function __construct(int $delta, int $forwardDelta = null)
    {
        if ($delta < 0 || $forwardDelta < 0) {
            throw new InvalidArgumentException("Parameters must be greater than or equal to 0.");
        }

        $this->delta = $delta;
        $this->forwardDelta = $forwardDelta;
    }

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
        $date = $value instanceof Carbon ? $value : Carbon::parse($value);

        if ($this->delta && (is_null($this->forwardDelta) || $this->forwardDelta !== 0))

        return $date->between(
            Carbon::now()->subSeconds($this->delta),
            Carbon::now()->addSeconds($this->forwardDelta ?? $this->delta)
        );
    }

    /**
     * Get the validation error message.
     *
     * @return string - The validation error message.
     */
    public function message()
    {
        return 'The provided datetime wasn\'t within the given delta';
    }
}
