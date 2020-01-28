<?php

namespace Bluewing\Concerns;

use Closure;
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
        static::addGlobalScope(new TenancyScope);

        static::creating(function(Model $model) {
            if ($this->canSetOrganizationIdentifier($model)) {
                $model->{$this->organizationIdentifierKey()} = auth()->user()->{$this->organizationIdentifierKey()};
            }
        });
    }

    /**
     * Any model which has a scope of a tenancy has a corresponding relationship to an `Organization`
     * which can be defined in this trait.
     *
     * @laravel-relation `HasTenancyScope` belongsTo `Organization`.
     *
     * @return BelongsTo - The relationship this model has an to `Organization`.
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(
            config('bluewing.tenancies.organization.model'),
            $this->organizationIdentifierKey()
        );
    }

    /**
     * The `Organization` identifier can only be set on a model where it has not already been set,
     * and where the user is authenticated.
     *
     * @param Model $model - The tenantable `Model` that is being created.
     *
     * @return bool - `true` if the ID of the `Organization` can be set on the model, `false` otherwise.
     */
    private function canSetOrganizationIdentifier(Model $model): bool
    {
        return !isset($model->{$this->organizationIdentifierKey()})
            && auth()->check();
    }

    /**
     * Helper function to retrieve the identifier for the `Organization`, stored in the application configuration.
     *
     * @return string - The `Organization` identifier key.
     */
    private function organizationIdentifierKey(): string
    {
        return config('bluewing.tenancies.organization.identifier');
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
     * Abstract definition for `addGlobalScope`, utilized by Eloquent models.
     *
     * @param Scope $scope
     *
     * @return mixed
     */
    public static abstract function addGlobalScope(Scope $scope);

    /**
     * Abstract definition for `creating`, utilized by Eloquent models.
     *
     * @param Closure $fn
     *
     * @return mixed
     */
    public static abstract function creating(Closure $fn);
}
