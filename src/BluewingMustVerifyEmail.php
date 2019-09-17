<?php

namespace Bluewing;

use Illuminate\Auth\Notifications\VerifyEmail;

trait BluewingMustVerifyEmail {

    /**
     *
     */
    public function hasVerifiedEmail() {
        return !is_null($this->emailVerifiedAt);
    }

    /**
     *
     */
    public function markEmailAsVerified() {
        return $this->forceFill([
            'emailVerifiedAt' => $this->freshTimestamp(),
        ])->save();
    }

    /**
     *
     */
    public function sendEmailVerificationNotification() {
        $this->notify(new VerifyEmail);
    }
}
