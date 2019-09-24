<?php

namespace Bluewing\Models;

use Bluewing\Model;
use Bluewing\Tenant;
use Bluewing\Contracts\TenantContract;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use function Bluewing\Helpers\getFullModelNamespace;

class Organization extends Model implements TenantContract
{
    use Tenant;

    /**
     * The name of the table in the database.
     */
    protected $table = 'Organizations';

    /**
     *
     */
    protected $guarded = ['id'];

    /**
     * An `Organization` is comprised of many `User`'s, both staff members and students, each having
     * varying levels of access to the `Organization`'s associated data.
     *
     * @laravel-relation `Organization` belongsToMany `User`.
     *
     * @return BelongsToMany
     */
    public function users() {
        $fullyNamespacedUserModel = getFullModelNamespace('User');
        return $this->belongsToMany($fullyNamespacedUserModel, 'UserOrganizations', 'organizationId', 'userId');
    }

    /**
     * An `Organization` is linked to its `User`'s through the `UserOrganization` pivot table.
     *
     * @laravel-relation `Organization` hasMany `UserOrganization`.
     *
     * @return HasMany
     */
    public function userOrganizations() {
        return $this->hasMany(getFullModelNamespace('UserOrganization'), 'organizationId');
    }
}
