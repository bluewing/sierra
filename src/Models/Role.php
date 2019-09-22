<?php

namespace Bluewing\Models;

use Bluewing\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Role extends Model
{
    /**
     * The name of the table in the database.
     */
    protected $table = 'Roles';

    /**
     *
     */
    protected $guarded = [];

    /**
     * Each user's `UserOrganization` may have many different `Role`'s, but an individual `Role` always
     * belongs to just one `UserOrganization`. For example, A `User` may have the organization owner `Role`
     * for one `UserOrganization`, but a student `Role` for another.
     *
     * @laravel-relation `Role` belongsTo `UserOrganization`.
     *
     * @return BelongsTo - The relationship.
     */
    public function userOrganization() {
        return $this->belongsTo('Bluewing\Models\UserOrganization', 'userOrganizationId');
    }
}
