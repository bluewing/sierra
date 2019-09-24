<?php

namespace Bluewing\Models;

use Bluewing\Model as BluewingModel;
use Bluewing\Scopes\HasTenancyScope;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use function Bluewing\Helpers\getFullModelNamespace;

class RefreshToken extends BluewingModel {

    use HasTenancyScope;

    /**
     * The name of the table in the database.
     */
    protected $table = 'RefreshTokens';

    /**
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * A `RefreshToken` also belongs to an `Organization`. This allows the parent `Organization` to
     * exert control over the login state of its `User`'s, and the devices they are using.
     *
     * @laravel-relation `RefreshToken` belongsTo `Organization`.
     *
     * @returns BelongsTo
     */
    public function organization() {
        return $this->belongsTo(getFullModelNamespace('Organization'));
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
        return $this->belongsTo(getFullModelNamespace('UserOrganization'));
    }
}
