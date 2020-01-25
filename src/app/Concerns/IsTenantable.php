<?php

namespace Bluewing\Concerns;

use Bluewing\Eloquent\Model;
use Bluewing\Scopes\TenancyScope;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Scope;

/**
 * A trait which provides the functionality of the `TenancyScope` scope to traited models.
 *
 * @package Bluewing\Concerns
 */
trait IsTenantable {

    /**
     * When the `IsTenantable` trait is booted, ensure any queries have the `TenancyScope` global scope
     * applied, and any created model is given the appropriate organization identifier.
     *
     * @return void
     */
    protected static function bootIsTenantable()
    {
        $organizationIdentifier = config('bluewing.tenancies.organization.identifier');

        static::addGlobalScope(new TenancyScope);

        static::creating(function(Model $model) use ($organizationIdentifier) {
            if (!isset($model->{$organizationIdentifier})) {
                $model->{$organizationIdentifier} = auth()->user()->{$organizationIdentifier};
            }
        });
    }

    /**
     * Any model which has a scope of a tenancy has a corresponding relationship to an `Organization`
     * which can be defined in this trait.
     *
     * @laravel-relation `HasTenancyScope` belongsTo `Organization`.
     *
     * @return BelongsTo - The relationship this model has an to `Organization`
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(
            config('bluewing.tenancies.organization.model'),
            config('bluewing.tenancies.organization.identifier')
        );
    }

    /**
     * Abstract definition for `belongsTo`, utilized by Eloquent models.
     *
     * @param $related
     * @param null $foreignKey
     * @param null $ownerKey
     * @param null $relation
     *
     * @return BelongsTo
     */
    public abstract function belongsTo($related, $foreignKey = null, $ownerKey = null, $relation = null);

    /**
     * @param Scope $scope
     *
     * @return mixed
     */
    public static abstract function addGlobalScope(Scope $scope);
}
