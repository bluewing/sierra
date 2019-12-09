<?php

namespace Bluewing\Rules;

use Illuminate\Auth\AuthManager;
use Illuminate\Contracts\Validation\Rule;

class UserOrganizationIsNewRule implements Rule
{

    /**
     * @var AuthManager
     */
    protected AuthManager $auth;

    /**
     * UserOrganizationIsNewRule constructor.
     *
     * @param AuthManager $auth
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
        $userOrganizationModel = createModel(config('tenancies.userOrganization.model'));

        $result = $userOrganizationModel->newQuery()
            ->where('organizationId', $this->auth->user()->organizationId)
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
