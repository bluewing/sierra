<?php

namespace Bluewing\Rules;

use Illuminate\Contracts\Validation\Rule;

class UserHasVerifiedEmailRule implements Rule
{
    /**
     * Validation rule that should return `true` if the `User` at the associated `UserOrganization` ID has a verified
     * email address, `false` otherwise.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     *
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $userOrganizationModel = createModel(config('bluewing.tenancies.userOrganization.model'));

        return !is_null(
            $userOrganizationModel->newQuery()->with('user')->findOrFail($value)->user->emailVerifiedAt
        );
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The user\'s email address is not verified.';
    }
}
