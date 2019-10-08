<?php

namespace Bluewing\Contracts;

/**
 * An empty interface representing the contract that `Organization` adheres to. This may be used in the
 * future to provide tenancy-related functionality.
 *
 * @see `Bluewing\Models\Organization`
 */
interface OrganizationContract {

    /**
     * @return mixed
     */
    public function getIdentifier(): string;

    /**
     * @return mixed
     */
    public function setIdentifier();
}
