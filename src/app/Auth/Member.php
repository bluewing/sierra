<?php


namespace Bluewing\Auth;


use Bluewing\Eloquent\Pivot as BluewingPivot;

use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

use Bluewing\Auth\Concerns\Authenticatable;
use Bluewing\Auth\Concerns\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Bluewing\Notifications\Notifiable;

class Member extends BluewingPivot implements AuthenticatableContract, AuthorizableContract, CanResetPasswordContract
{
    use Authenticatable, CanResetPassword, Authorizable, Notifiable;

    /**
     * The name of the table in the database.
     *
     * @var string
     */
    protected $table = 'Members';
}
