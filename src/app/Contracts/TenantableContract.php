<?php


namespace Bluewing\Contracts;


use Illuminate\Database\Eloquent\Relations\BelongsTo;

interface TenantableContract
{
    /**
     * @return BelongsTo
     */
    public function organization(): BelongsTo;

    /**
     * @return bool
     */
    public function canSetOrganizationIdentifier(): bool;

    /**
     * @return string
     */
    public function organizationIdentifierKey(): string;
}
