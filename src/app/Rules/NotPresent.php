<?php

namespace Bluewing\Rules;

use Illuminate\Contracts\Validation\Rule;

class NotPresent implements Rule
{
    /**
     * The provided attribute should not be present in the `Request` object. Returns `true` if the attribute is not
     * present, or `false` otherwise.
     *
     * @param  string  $attribute - The name of the attribute to check for the presence in the request.
     * @param  mixed  $value - The value of the attribute, if it exists.
     *
     * @return bool - `true` if the rule passes, `false` otherwise.
     */
    public function passes($attribute, $value): bool
    {
        return !array_key_exists($attribute, request()->input());
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return ':attribute attribute found, but it should not be present.';
    }
}
