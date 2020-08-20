<?php

namespace Bluewing\Auth\Concerns;

use Bluewing\Eloquent\Model;
use Illuminate\Auth\Notifications\VerifyEmail;

/**
 * Trait BluewingMustVerifyEmail
 *
 * @property Model user - The `User` model that is related to the model that traits the `MustVerifyEmail` functionality.
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
        return ! is_null($this->user->emailVerifiedAt);
    }

    /**
     * Mark the given user's email as verified.
     *
     * @return bool
     */
    public function markEmailAsVerified()
    {
        return $this->user->forceFill([
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
        return $this->user->email;
    }
}
