<?php

namespace Bluewing\Models;

use Bluewing\BluewingModel;
use Bluewing\Scopes\HasTenancyScope;

class RefreshToken extends BluewingModel {

    use HasTenancyScope;

    /**
     * The name of the table in the database.
     */
    protected $table = 'RefreshTokens';

    /**
     * A `RefreshToken` also belongs to an `Organization`. This allows the parent `Organization` to
     * exert control over the login state of its `User`'s, and the devices they are using.
     *
     * @laravel-relation `RefreshToken` belongsTo `Organization`.
     *
     * @returns BelongsTo
     */
    public function organization() {
        return $this->belongsTo('Bluewing\Models\Organization');
    }

    /**
     * A `RefreshToken` is related to a `UserOrganization` uniquely. It represents a
     * single login instance for the current `User` tjat is valid for only one `UserOrganization`
     * on the specified device.
     *
     * @laravel-relation `RefreshToken` belongsTo `UserOrganization`.
     *
     * @returns BelongsTo
     */
    public function userOrganization() {
        return $this->belongsTo('Bluewing\Models\UserOrganization');
    }
}