<?php


namespace Bluewing\Auth;

use Bluewing\Eloquent\Model as BluewingModel;

use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;

use Bluewing\Auth\Concerns\MustVerifyEmail;
use Illuminate\Notifications\Notifiable;

class User extends BluewingModel implements MustVerifyEmailContract
{
    use MustVerifyEmail, Notifiable;
}