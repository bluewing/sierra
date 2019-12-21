<?php

namespace Bluewing\Rules;

use Illuminate\Contracts\Validation\Rule;

class MatchesRule implements Rule
{
    /**
     * The string that should be matched.
     *
     * @var string
     */
    protected string $match;

    /**
     * Create a new rule instance.
     *
     * @param string $match
     */
    public function __construct(string $match)
    {
        $this->match = $match;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     *
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return $this->match === $value;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return ':value does not match the expected value.';
    }
}
