<?php

namespace Bluewing\Contracts;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * An empty interface representing the contract that `Organization` adheres to. This may be used in the
 * future to provide tenancy-related functionality.
 *
 * @see `Bluewing\Models\Organization`
 */
interface OrganizationContract
{
    /**
     * @return HasMany
     */
    public function userOrganizations(): HasMany;

    /**
     * @return BelongsToMany
     */
    public function users(): BelongsToMany;
}
