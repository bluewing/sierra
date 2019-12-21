<?php

namespace Bluewing\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class UserOrganizationIsNewRule implements Rule
{

    /**
     * UserOrganizationIsNewRule constructor.
     */
    public function __construct()
    {
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $userOrganizationModel = createModel(config('bluewing.tenancies.userOrganization.model'));

        $result = $userOrganizationModel->newQuery()
            ->where('organizationId', Auth::user()->organizationId)
            ->whereHasEmail($value)
            ->first();

        return is_null($result);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'This user already exists for this organization.';
    }
}
