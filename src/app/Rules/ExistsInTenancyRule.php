<?php

namespace Bluewing\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ExistsInTenancyRule implements Rule
{

    /**
     * The name of the table in the database to execute a search for.
     */
    protected $databaseTable;

    /**
     * The name of the column in the table to execute a search against.
     */
    protected $databaseColumn;

    /**
     * Constructor for ExistsInTenancyRule.
     *
     * @param string $databaseTable - The string representing the database table that should be queried.
     * @param string $databaseColumn - The string representing the database column that should be queried.
     */
    public function __construct($databaseTable, $databaseColumn)
    {
        $this->databaseTable = $databaseTable;
        $this->databaseColumn = $databaseColumn;
    }

    /**
     * Executes a tenancy-aware query to retrieve an item with the prescribed value at the
     * database table and column as provided.
     *
     * @param $attribute
     * @param $value
     *
     * @return boolean - `true` if the validation rule passed successfully, `false` otherwise.
     */
    public function passes($attribute, $value)
    {
        $result = DB::table($this->databaseTable)
            ->where('organizationId', Auth::user()->organizationId)
            ->where($this->databaseColumn, $value)
            ->first();

        return !is_null($result);
    }

    /**
     * Get the validation error message.
     *
     * @return string - The validation error message.
     */
    public function message()
    {
        return 'There is no valid existing value for that.';
    }
}
