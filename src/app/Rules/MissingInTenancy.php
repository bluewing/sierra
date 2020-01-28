<?php

namespace Bluewing\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MissingInTenancy implements Rule
{

    /**
     * The rule to base our decision off.
     *
     * @var ExistsInTenancy
     */
    protected ExistsInTenancy $rule;

    /**
     * Constructor for DoesNotExistInTenancyRule. This rule provides the opposite functionality of `ExistsInTenancyRule`,
     * so it is acceptable to instantiate an instance of that rule and return the opposite result.
     *
     * @param string $databaseTable - The string representing the database table that should be queried.
     * @param string|null $databaseColumn - The string representing the database column that should be queried. If not
     * provided, defaults to 'id'.
     */
    public function __construct(string $databaseTable, ?string $databaseColumn = 'id')
    {
        $this->rule = new ExistsInTenancy($databaseTable, $databaseColumn);
    }

    /**
     * Executes a tenancy-aware query to retrieve an item with the prescribed value at the
     * database table and column as provided. Should return `true` if the value is missing in the tenancy, `false`
     * otherwise.
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
     * Get the validation error message.
     *
     * @return string - The validation error message.
     */
    public function message()
    {
        return ':attribute with a value of :value already exists in your organization.';
    }
}
