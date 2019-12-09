<?php

namespace Bluewing\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class UserOrganizationIsNewRule implements Rule
{

    /**
     * @var AuthManager
     */
    protected AuthManager $auth;

    /**
     * UserOrganizationIsNewRule constructor.
     *
     * @param AuthManager - The dependency-injected instance of AuthManager.
     */
    public function __construct(AuthManager $auth)
    {
        $this->auth = $auth;
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
        $result = UserOrganization::where('organizationId', $this->auth->user()->organizationId)
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
