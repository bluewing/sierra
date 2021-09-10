<?php

namespace Bluewing\Rules;

use Bluewing\Rules\Support\HasCustomizableMessage;
use Illuminate\Contracts\Validation\Rule;

class MissingInTenancy implements Rule
{
    use HasCustomizableMessage;

    /**
     * The rule to use that provides the inverse functionality.
     *
     * @var ExistsInTenancy
     */
    protected ExistsInTenancy $rule;

    /**
     * Constructor for `MissingInTenancy`. This rule provides the opposite functionality of `ExistsInTenancy`, so it
     * is acceptable to instantiate an instance of that rule and return the opposite result.
     *
     * @param string $databaseTable - The string representing the database table that should be queried.
     */
    public function __construct(string $databaseTable)
    {
        $this->rule = ExistsInTenancy::inTable($databaseTable);
    }

    /**
     * Static constructor function that more fluently constructs an instance of the `MissingInTenancy` rule without
     * needing to `new` up a class in a `FormRequest`.
     *
     * @param string $databaseTable - The string representing the database table that should be queried.
     *
     * @return MissingInTenancy - A constructed instance of the `MissingInTenancy`.
     */
    public static function inTable(string $databaseTable): MissingInTenancy
    {
        return new static($databaseTable);
    }

    /**
     * Customizes the column that will be used to check for uniqueness.
     *
     * @param string $databaseColumn - The string representing the database column that should be queried. If this
     * method is not called, then the default is to fallback to `id`.
     *
     * @return MissingInTenancy - The modified `MissingInTenancy` `Rule`.
     */
    public function forColumn(string $databaseColumn): MissingInTenancy
    {
        $this->rule->forColumn($databaseColumn);
        return $this;
    }

    /**
     * Executes a tenancy-aware query to retrieve an item with the prescribed value at the database table and column
     * as provided. Should return `true` if the value is missing in the tenancy, `false` otherwise.
     *
     * @param $attribute
     * @param $value
     *
     * @return boolean - `true` if the validation rule passed successfully, `false` otherwise.
     */
    public function passes($attribute, $value)
    {
        return !$this->rule->passes($attribute, $value);
    }

    /**
     * Get the default validation error message.
     *
     * @return string - The default validation error message.
     */
    public function defaultMessage(): string
    {
        return 'The :attribute has already been taken.';
    }
}
