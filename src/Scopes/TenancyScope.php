<?php

namespace Bluewing\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class TenancyScope implements Scope {

    /**
     * Applies a scope to all models that prevents retrievals of models that are outside of the currently
     * authenticated `UserOrganization`'s tenancy. `TenancyScope` is therefore implemented by nearly all
     * models in a Bluewing application, and is specifically traited by `HasTenancyScope`, which is included in
     * `Model` and `Pivot`.
     *
     * @param Builder $builder
     * @param Model $model
     *
     * @return Builder
     */
    public function apply(Builder $builder, Model $model)
    {
        if (Auth::check()) {
            return $builder->where('organizationId', Auth::user()->organizationId);
        }
        return $builder;
    }
}
