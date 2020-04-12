<?php


namespace Bluewing\Auth;


use Bluewing\Eloquent\Pivot as BluewingPivot;

use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

use Bluewing\Auth\Concerns\Authenticatable as BluewingAuthenticatable;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Bluewing\Auth\Concerns\CanResetPassword as BluewingCanResetPassword;
use Bluewing\Auth\Concerns\MustVerifyEmail as BluewingMustVerifyEmail;

class Member extends BluewingPivot implements
    AuthenticatableContract,
    AuthorizableContract,
    CanResetPasswordContract
{
    use BluewingAuthenticatable, Authorizable, BluewingCanResetPassword, BluewingMustVerifyEmail;
}
