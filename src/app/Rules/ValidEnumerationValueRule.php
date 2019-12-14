<?php

namespace Bluewing\Rules;

use Illuminate\Contracts\Validation\Rule;
use UnexpectedValueException;

class ValidEnumerationValueRule implements Rule
{
    /**
     * @var string
     */
    protected string $enumerationClass;

    /**
     * Create a new rule instance.
     *
     * @param string $enumerationClass - The dependency-injected instanced of the namespace of the
     * enumeration class.
     *
     * @return void
     */
    public function __construct(string $enumerationClass)
    {
        $this->enumerationClass = $enumerationClass;
    }

    /**
     * Determine if the validation rule passes by attempting to instantiate the enumeration class
     * with the provided value. If an exception is thrown, the value does not exist on the enumeration.
     *
     * @see https://github.com/myclabs/php-enum/blob/master/src/Enum.php#L41-L52
     *
     * @param  string  $attribute
     * @param  mixed  $value
     *
     * @return bool
     */
    public function passes($attribute, $value)
    {
        try {
            new $this->enumerationClass(intval($value));
            return true;
        } catch (UnexpectedValueException $e) {
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
}
