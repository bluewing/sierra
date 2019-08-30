<?php

namespace Bluewing\SharedServer;

use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;

trait BluewingCanResetPassword
{
    public function getEmailForPasswordReset() {
        return $this->user->email;
    }

    public function sendPasswordResetNotification($token) {
        $this->notify(new ResetPasswordNotification($token));
    }
}
