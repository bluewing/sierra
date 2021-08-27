<?php

namespace Bluewing\Auth\Concerns;

use Illuminate\Auth\Passwords\CanResetPassword as BaseCanResetPassword;

trait CanResetPassword
{
    use BaseCanResetPassword;

    /**
     * Get the e-mail address where password reset links are sent.
     *
     * @return string
     */
    public function getEmailForPasswordReset()
    {
        return $this->user->email;
    }
}
