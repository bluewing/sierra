<?php

namespace Bluewing\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;

class ExistsInTenancy implements Rule
{
    /**
     * The name of the table in the database to execute a search for.
     */
    protected string $databaseTable;

    /**
     * The name of the column in the table to execute a search against.
     */
    protected ?string $databaseColumn;

    /**
     * Constructor for `ExistsInTenancy`.
     *
     * @param string $databaseTable - The string representing the database table that should be queried.
     * @param string|null $databaseColumn - The string representing the database column that should be queried. If not
     * provided, defaults to `id`.
     */
    public function __construct(string $databaseTable, ?string $databaseColumn = 'id')
    {
        $this->databaseTable = $databaseTable;
        $this->databaseColumn = $databaseColumn;
    }

    /**
     * Executes a tenancy-aware query to retrieve an item with the prescribed value at the database table and column as
     * provided. Should return `true` if the database value exists in the tenancy, `false` otherwise. If the value
     * provided is an array, a `whereIn` query will be executed to more efficiently validate multiple values
     * simultaneously.
     *
     * @param $attribute - The attribute name that is being checked.
     * @param $value - The value to check against.
     *
     * @return boolean - `true` if the validation rule passed successfully, `false` otherwise.
     */
    public function passes($attribute, $value)
    {
        $tenancyQuery = DB::table($this->databaseTable)->where('organizationId', auth()->user()->organizationId);

        if (is_array($value)) {
            return $tenancyQuery->whereIn($this->databaseColumn, $value)->count() === count($value);
        }
        return !is_null($tenancyQuery->where($this->databaseColumn, $value)->first());
    }

    /**
     * Get the validation error message.
     *
     * @return string - The validation error message.
     */
    public function message()
    {
        return ':attribute with a value of :value does not exist in your organization.';
    }
}
