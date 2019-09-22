<?php

namespace Bluewing\Models;

use Bluewing\BluewingCanResetPassword;
use Bluewing\Model;
use Bluewing\BluewingMustVerifyEmail;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;

class User extends Model implements MustVerifyEmailContract, CanResetPasswordContract
{
    use Notifiable, BluewingMustVerifyEmail, BluewingCanResetPassword;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'emailVerifiedAt' => 'datetime',
    ];

    /**
     * The name of the table in the database.
     */
    protected $table = 'Users';

    /**
     * A `User`, either a student or an instructor, can be part of many `Organization`'s that use Horizon.
     * This is supported via a many to many relationship between the two entities.
     *
     * @laravel-relation `User` belongsToMany `Organization`'s.
     *
     * @return BelongsToMany
     */
    public function organizations() {
        return $this->belongsToMany('Bluewing\Models\Organization', 'UserOrganizations', 'userId', 'organizationId');
    }

    /**
     * The `UserOrganization` is the pivot between a `User` and an `Organization`, and represents a `Users`'s
     * link with an `Organization`. As such, a `User` can have many `UserOrganization`'s.
     *
     * @laravel-relation `User` hasMany `UserOrganization`'s.
     *
     * @return HasMany
     */
    public function userOrganizations() {
        return $this->hasMany('Bluewing\Models\UserOrganization', 'userId');
    }
}
