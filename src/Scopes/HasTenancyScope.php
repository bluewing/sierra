<?php

namespace Bluewing\Scopes;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Scope;

/**
 * A trait which provides the functionality of the `TenancyScope` scope to traited models.
 */
trait HasTenancyScope {

    /**
     * @return void
     */
    protected static function bootWithTenancy()
    {
        static::addGlobalScope(new TenancyScope);
    }

    /**
     * Any model which has a scope of a tenancy has a corresponding relationship to an `Organization`
     * which can be defined in this trait.
     *
     * @laravel-relation `HasTenancyScope` belongsTo `Organization`.
     *
     * @return BelongsTo - The relationship this model has an to `Organization`
     */
    public function organization()
    {
        return $this->belongsTo(
            config('bluewing.tenancies.organization.model'),
            config('bluewing.tenancies.organization.identifier')
        );
    }

    /**
     * @param $related
     * @param null $foreignKey
     * @param null $ownerKey
     * @param null $relation
     * @return mixed
     */
    public abstract function belongsTo($related, $foreignKey = null, $ownerKey = null, $relation = null);

    /**
     * @param Scope $scope
     *
     * @return mixed
     */
    public static abstract function addGlobalScope(Scope $scope);
}
