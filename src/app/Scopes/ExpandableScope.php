<?php


namespace Bluewing\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class ExpandableScope implements Scope
{

    /**
     * Apply the expandable scope to all eloquent models. If the `expand` query parameter
     * is present in the URL, the requested related models will be included in the query
     * output.
     *
     * TODO: Evaluate how to prevent unauthorized access to related models from this
     * method. Including both tenancy-escape and authorization-escape scenarios.
     *
     * @param Builder $builder
     * @param Model $model
     *
     * @return Builder
     */
    public function apply(Builder $builder, Model $model)
    {
        if (request()->has('expand')) {
            $expandables = request()->query('expand');

            if (!is_array($expandables)) {
                $expandables = [$expandables];
            }

            return $builder->with($expandables);
        }

        return $builder;
    }
}
