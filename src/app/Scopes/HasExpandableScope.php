<?php


namespace Bluewing\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

/**
 * A trait which provides the functionality of the `expands` scope to traited models.
 *
 * @package Bluewing\Scopes
 */
trait HasExpandableScope
{
    /**
     * Apply the expandable scope to all eloquent models. If the `expand` query parameter
     * is present in the URL, the requested related models will be included in the query
     * output.
     *
     * TODO: Evaluate how to prevent unauthorized access to related models from this
     * method. Including both tenancy-escape and authorization-escape scenarios.
     *
     * TODO: Figure out how to prevent non-relations of the given model from being included here.
     *
     * @param Builder $builder
     *
     * @return Builder
     */
    public function scopeExpands(Builder $builder)
    {
        if (request()->has('expand')) {
            return $builder->with(
                Arr::wrap(request()->query('expand'))
            );
        }

        return $builder;
    }
}
