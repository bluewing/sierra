<?php

namespace Bluewing\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class HasMatchingPasswordRule implements Rule
{
    /**
     * @var string
     */
    protected string $password;

    /**
     * Create a new rule instance.
     *
     * @param string $password - The dependency-injected instance of the hashed password to check.
     */
    public function __construct(string $password)
    {
        $this->password = $password;
    }

    /**
     * This rule will evaluate to `true` if the provided value matches the provided password. It will return `false`
     * otherwise.
     *
     * This is useful for evaluating whether someone has similar credentials to the given user, without necessarily
     * wanting  to log the `User` in. For example, if a user is creating a new `Organization`, to confirm they are
     * the same `User` beyond their email address, their name and password must also match.
     *
     * @param  string  $attribute - The attribute being evaluated.
     * @param  mixed  $value - The value of the password to evaluate to the given user's.
     *
     * @return bool - `true` if the rule passes, `false` otherwise.
     */
    public function passes($attribute, $value)
    {
        return Hash::check($value, $this->password);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The provided password does not match.';
    }
}
