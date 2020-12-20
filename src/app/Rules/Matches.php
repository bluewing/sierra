<?php

namespace Bluewing\Rules;

use Illuminate\Contracts\Validation\Rule;
use InvalidArgumentException;

class Matches implements Rule
{
    /**
     * An array of possible values that the attribute must have.
     *
     * @var array
     */
    protected array $possibleValues;

    /**
     * An optional condition that can be evaluated, if it's provided.
     */
    protected $condition;

    /**
     * Create a new rule instance.
     *
     * @param array $possibleValues - The possible values that the attribute can be.
     */
    public function __construct(array $possibleValues)
    {
        if (!is_array($possibleValues)) {
            throw new InvalidArgumentException('possible values must be an array');
        }

        $this->possibleValues = $possibleValues;
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
        return in_array($value, $this->possibleValues);

    }

    /**
     * @param callable $condition - An optional condition to be evaluated.
     *
     * @return $this - This instance of the `Matches` rule.
     */
    public function iff(callable $condition): Matches
    {
        $this->condition = $condition;
        return $this;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return ':attribute needs to equal ' . $this->possibleValues[0];
    }
}
