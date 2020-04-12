<?php

namespace Bluewing\Auth\Concerns;

use Carbon\Carbon;
use Illuminate\Auth\Notifications\VerifyEmail;

/**
 * Trait BluewingMustVerifyEmail
 *
 * @property string email - This property exists on all models that this trait traits.
 * @property Carbon emailVerifiedAt - This property exists on all models that this trait traits.
 *
 * @package Bluewing
 *
 * @method forceFill(array $properties);
 * @method freshTimestamp();
 * @method notify();
 *
 * @see Illuminate\Auth\MustVerifyEmail
 */
trait MustVerifyEmail {

    /**
     * Determine if the user has verified their email address.
     *
     * @return bool
     */
    public function hasVerifiedEmail()
    {
        return ! is_null($this->emailVerifiedAt);
    }

    /**
     * Mark the given user's email as verified.
     *
     * @return bool
     */
    public function markEmailAsVerified()
    {
        return $this->forceFill([
            'emailVerifiedAt' => $this->freshTimestamp(),
        ])->save();
    }

    /**
     * Send the email verification notification.
     *
     * @return void
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmail);
    }

    /**
     * Get the email address that should be used for verification.
     *
     * @return string
     */
    public function getEmailForVerification()
    {
        return $this->email;
    }
}
