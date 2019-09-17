<?php

namespace Bluewing\Models;

use Bluewing\BluewingAuthentication;
use Bluewing\BluewingPivot;
use Bluewing\Contracts\BluewingAuthenticationContract;
use Bluewing\Scopes\HasTenancyScope;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\Access\Authorizable;

class UserOrganization extends BluewingPivot implements BluewingAuthenticationContract, AuthorizableContract
{
    use HasTenancyScope, BluewingAuthentication, Authorizable;

    /**
     * The name of the table in the database.
     */
    protected $table = 'UserOrganizations';

    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * The `UserOrganization` is linked to a `User`, who may have other `Organization`'s which are out
     * of the scope of the current `UserOrganization`'s domain.
     *
     * @laravel-relation `UserOrganization` belongsTo `User`.
     *
     * @return BelongsTo
     */
    public function user() {
        return $this->belongsTo('Bluewing\Models\User', 'userId');
    }

    /**
     * The `UserOrganization` is one of many that forms an `Organization`. A `UserOrganization` could
     * be a staff member or a student for the `Organization`.
     *
     * @laravel-relation `UserOrganization` belongsTo `Organization`.
     *
     * @return BelongsTo
     */
    public function organization() {
        return $this->belongsTo('Bluewing\Models\Organization', 'organizationId');
    }

    /**
     * A `UserOrganization` has `Role`'s which represents the permissions granted to the `UserOrganization`
     * to perform actions on the Horizon application.
     *
     * @laravel-relation `UserOrganization` hasMany `Role`'s.
     *
     * @return HasMany
     */
    public function roles() {
        return $this->hasMany('Bluewing\Models\Role', 'userOrganizationId');
    }

    /**
     * A `UserOrganization` can have one or more `RefreshToken`'s, each one residing on the
     * device on which the `UserOrganization` is logged in on.
     *
     * @laravel-relation `UserOrganization` hasMany `RefreshToken`'s.
     *
     * @return HasMany
     */
    public function refreshTokens() {
        return $this->hasMany('Bluewing\Models\RefreshToken', 'userOrganizationId');
    }
}
