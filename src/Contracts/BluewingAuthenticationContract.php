<?php

namespace Bluewing\SharedServer\Contracts;

use Illuminate\Contracts\Auth\Authenticatable;

/**
 * `BluewingAuthenticationContract` represents the interface that a `UserOrganization` model
 * adheres to. It extends `Illuminate\Contracts\Auth\Authenticatable`, which includes definitions
 * for retrieving auth identifiers and passwords.
 *
 * `BluewingAuthenticationContract` further extends this to provide methods to retrieve the associated
 * `User` and `Organization` related to the implementation of this interface.
 *
 * `JwtManager`'s `buildTokenFor` method expects a model adhering to this interface as its first argument.
 *
 * @see Illuminate\Contracts\Auth\Authenticatable
 * @see App\Models\UserOrganization
 */
interface BluewingAuthenticationContract extends Authenticatable {

    /**
     * Retrieves the `User` associated with this contract's implementation.
     *
     * @return App\Model\User
     */
    public function getUser();

    /**
     * Retrieves the `Organization` associated with this contract's implementation.
     *
     * @return App\Models\Organization
     */
    public function getTenant();
}
