<?php


namespace Bluewing\Auth;


use Bluewing\Eloquent\Pivot as BluewingPivot;

use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;

use Bluewing\Auth\Concerns\Authenticatable as BluewingAuthenticatable;
use Bluewing\Auth\Concerns\CanResetPassword as BluewingCanResetPassword;
use Bluewing\Auth\Concerns\MustVerifyEmail as BluewingMustVerifyEmail;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Notifications\Notifiable;

class Member extends BluewingPivot implements
    AuthenticatableContract, AuthorizableContract,
    CanResetPasswordContract, MustVerifyEmailContract
{
    use BluewingAuthenticatable, BluewingCanResetPassword, BluewingMustVerifyEmail, Authorizable, Notifiable;
}
