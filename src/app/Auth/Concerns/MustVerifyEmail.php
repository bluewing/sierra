<?php

namespace Bluewing\Auth\Concerns;

use Illuminate\Auth\MustVerifyEmail as BaseMustVerifyEmail;

/**
 * Provides functionality for the model that can verify an email address. This assumes the existence of an
 * `emailVerifiedAt` and `email` property on the model. For example, in showcrew, both an organization and a user can
 * verify addresses.
 *
 * @package Bluewing
 *
 * @method forceFill(array $properties);
 * @method freshTimestamp();
 *
 * @see Illuminate\Contracts\Auth\MustVerifyEmail - The interface that is fulfilled by providing this trait on a model.
 * @see Illuminate\Auth\MustVerifyEmail - The initial trait which this trait overrides. Some methods are not redefined.
 * @see \Illuminate\Auth\Notifications\VerifyEmail - The email that is sent as part of the verification process.
 */
trait MustVerifyEmail {

    use BaseMustVerifyEmail;

    /**
     * Determine if the verifyee has verified their email address.
     *
     * @return bool
     */
    public function hasVerifiedEmail()
    {
        return ! is_null($this->emailVerifiedAt);
    }

    /**
     * Mark the given verifyee's email as verified.
     *
     * @return bool
     */
    public function markEmailAsVerified()
    {
        return $this->forceFill([
            'emailVerifiedAt' => $this->freshTimestamp(),
        ])->save();
    }
}
