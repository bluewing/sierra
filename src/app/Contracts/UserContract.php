<?php


namespace Bluewing\Contracts;


use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

interface UserContract
{
    /**
     * Retrieves the `Member`'s associated with this contract's implementation.
     *
     * @return HasMany
     */
    public function members(): HasMany;

    /**
     * Retrieves the `Organization`'s associated with this contract's implementation.
     *
     * @return BelongsToMany
     */
    public function organizations(): BelongsToMany;
}
