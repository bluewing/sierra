<?php


namespace Bluewing\Auth;

use Bluewing\Eloquent\Model as BluewingModel;

use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;

use Bluewing\Auth\Concerns\MustVerifyEmail;
use Bluewing\Notifications\Notifiable;

class User extends BluewingModel implements MustVerifyEmailContract
{
    use MustVerifyEmail, Notifiable;

    /**
     * The name of the table in the database.
     *
     * @var string
     */
    protected $table = 'Users';
}