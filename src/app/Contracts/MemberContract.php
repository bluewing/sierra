<?php

namespace Bluewing\Contracts;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * `MemberContract` represents the interface that a `Member` model adheres to. It extends
 * `Illuminate\Contracts\Auth\Authenticatable`, which includes definitions for retrieving auth identifiers and
 * passwords.
 *
 * `MemberContract` further extends this to provide methods to retrieve the associated `User` and `Organization`
 * related to the implementation of this interface.
 *
 * `JwtManager`'s `buildTokenFor` method expects a model adhering to this interface as its first argument.
 *
 * @see \Illuminate\Contracts\Auth\Authenticatable
 */
interface MemberContract extends Authenticatable {

    /**
     * Retrieves the `User` associated with this contract's implementation.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo;

    /**
     * Retrieves the `Organization` associated with this contract's implementation.
     *
     * @return BelongsTo
     */
    public function organization(): BelongsTo;

    /**
     * Retrieves the `Role`'s associated with this contract's implementation.
     *
     * @return HasMany
     */
    public function roles(): HasMany;
}
