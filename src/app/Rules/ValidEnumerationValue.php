<?php

namespace Bluewing\Rules;

use Illuminate\Contracts\Validation\Rule;
use UnexpectedValueException;

class ValidEnumerationValue implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @param string $enumerationClass - The dependency-injected instanced of the namespace of the
     * enumeration class.
     *
     * @return void
     */
    public function __construct(protected string $enumerationClass) {}

    /**
     * Determine if the validation rule passes by attempting to instantiate the enumeration class
     * with the provided value. If an exception is thrown, the value does not exist on the enumeration.
     *
     * @see https://github.com/myclabs/php-enum/blob/master/src/Enum.php#L41-L52.
     *
     * @param $attribute - The attribute that is being checked.
     * @param $value - The value that is being validated.
     *
     * @return bool - `true` if the validation rule passed successfully, `false` otherwise.
     */
    public function passes($attribute, $value)
    {
        try {
            $this->instantiateEnumeration(intval($value));
            return true;
        } catch (UnexpectedValueException) {
            return false;
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The value :value is not valid for the property :attribute.';
    }

    /**
     * @param $value
     *
     * @return mixed
     */
    protected function instantiateEnumeration($value): mixed
    {
        return new $this->enumerationClass($value);
    }
}
