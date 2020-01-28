<?php

namespace Bluewing\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class TenancyScope implements Scope {

    /**
     * Applies a scope to all models that prevents retrievals of models that are outside of the currently
     * authenticated `UserOrganization`'s tenancy. `TenancyScope` is therefore implemented by nearly all
     * models in a Bluewing application, and is specifically traited by `HasTenancyScope`, which is included in
     * `Model` and `Pivot`.
     *
     * @param Builder $builder - An instance of the eloquent query `Builder` class.
     * @param Model $model - The `Model` the scope is being applied to.
     *
     * @return Builder - Pass back the instance of the `Builder`, with the scope applied.
     */
    public function apply(Builder $builder, Model $model)
    {
        if (!auth()->check()) return $builder;

        return $builder->where($this->tenancyColumn($model), $this->tenancyValue());
    }

    /**
     * Helper method to get the column name that tenancy is filtered by, appended to the name of the model table that
     * is being retrieved.
     *
     * @param Model $model - The model associated with the column.
     *
     * @return string - The column name that should be used to identify a tenancy.
     */
    private function tenancyColumn(Model $model): string
    {
        return $model->getTable() . '.' . config('bluewing.tenancies.organization.identifier');
    }

    /**
     * Helper method to get the value that the tenancy is filtered by.
     *
     * @return string - The GUID for the `Organization` for the current tenancy.
     */
    private function tenancyValue(): string
    {
        return auth()->user()->{config('bluewing.tenancies.organization.identifier')};
    }
}
