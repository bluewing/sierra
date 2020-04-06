<?php

namespace Bluewing\Rules;

use Illuminate\Contracts\Validation\Rule;

class MemberIsNew implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $memberModel = createModel(config('bluewing.tenancies.member.model'));

        $result = $memberModel->newQuery()
            ->where('organizationId', auth()->user()->organizationId)
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
