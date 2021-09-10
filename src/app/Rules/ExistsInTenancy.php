<?php

namespace Bluewing\Rules;

use Bluewing\Rules\Support\HasCustomizableMessage;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;

class ExistsInTenancy implements Rule
{
    use HasCustomizableMessage;

    /**
     * The string representing the database column that should be queried. If not provided, defaults to `id`.
     *
     * @var string
     */
    protected string $databaseColumn = 'id';

    /**
     * Constructor for `ExistsInTenancy`.
     *
     * @param string $databaseTable - The string representing the database table that should be queried.
     */
    public function __construct(protected string $databaseTable) {}

    /**
     * Static constructor function that more fluently constructs an instance of the `ExistsInTenancy` rule without
     * needing to `new` up a class in a `FormRequest`.
     *
     * @param string $databaseTable - The string representing the database table that should be queried.
     *
     * @return ExistsInTenancy - A constructed instance of the `ExistsInTenancy` rule.
     */
    public static function inTable(string $databaseTable): ExistsInTenancy
    {
        return new static($databaseTable);
    }

    /**
     * Customizes the column that will be used to check for uniqueness.
     *
     * @param string $databaseColumn - The string representing the database column that should be queried. If this
     * method is not called, then the default is to fallback to `id`.
     *
     * @return ExistsInTenancy - The modified `ExistsInTenancy` `Rule`.
     */
    public function forColumn(string $databaseColumn): ExistsInTenancy
    {
        $this->databaseColumn = $databaseColumn;
        return $this;
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

        return is_array($value)
            ? $tenancyQuery->whereIn($this->databaseColumn, $value)->count() === count($value)
            : !is_null($tenancyQuery->where($this->databaseColumn, $value)->first());
    }

    /**
     * Get the default validation error message.
     *
     * @return string - The default validation error message.
     */
    public function defaultMessage(): string
    {
        return 'The :attribute with a value of ":input" does not exist in your organisation.';
    }
}
