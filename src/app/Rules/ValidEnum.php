<?php

namespace Bluewing\Rules;

use Illuminate\Contracts\Validation\Rule;
use TypeError;
use ValueError;

class ValidEnum implements Rule
{
    /**
     * `ValidEnum` tests if the provided value is a member of the provided enumeration.
     *
     * @param string $enum The dependency-injected instanced of the namespace of the enumeration.
     *
     * @return void
     */
    public function __construct(protected string $enum) {}

    /**
     * Determine if the validation rule passes by attempting to instantiate the enumeration class with the provided
     * value. If a `ValueError` is thrown, the value does not exist on the enumeration.
     *
     * @param mixed $attribute The attribute that is being checked.
     * @param mixed $value The value that is being validated.
     *
     * @return bool `true` if the validation rule passed successfully, `false` otherwise.
     */
    public function passes($attribute, $value)
    {
        try {
            $this->hydrateEnumerationFromBackedValue($value);
            return true;
        } catch (TypeError|ValueError) {
            return false;
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string The error message to be displayed to the user if the validation rule fails.
     */
    public function message()
    {
        return 'The value :value is not valid for the property :attribute.';
    }

    /**
     * Given a potentially valid enumeration value, hydrate the enumeration from the backed value using the `from`
     * static method. If the value is not part of the enumeration, a `ValueError` will be thrown. If the value is
     * neither an integer or string, then a `TypeError` will be thrown.
     *
     * @param string|int $value The value to attempt to hydrate a backed enumeration from.
     *
     * @return mixed The hydrated enumeration. This is later discarded.
     */
    protected function hydrateEnumerationFromBackedValue(string|int $value): mixed
    {
        return $this->enum::from($value);
    }
}
