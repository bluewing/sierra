<?php

namespace Bluewing\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

/**
 * Applies a scope to all models that prevents retrievals of models that are outside of the currently
 * authenticated `User`'s organizational scope. `TenancyScope` is therefore implemented by all models
 * in a Bluewing application, and is specifically traited by `HasTenancyScope`, which is included in
 * `Model` and `BluewingPivot`.
 */
class TenancyScope implements Scope {

    /**
     *
     */
    public function apply(Builder $builder, Model $model) {
        if (Auth::check()) {
            return $builder->where('organizationId', Auth::user()->organizationId);
        }
        return $builder;
    }
}
