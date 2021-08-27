<?php


namespace Bluewing\Http\Filters;


use Illuminate\Database\Eloquent\Builder;

trait AllowsFiltering
{
    /**
     * Provides a scoped-query that applies a `RequestFilter`'s to the `Builder` instance. This trait should be used
     * on any model that supports filtering.
     *
     * @param Builder $query - The `Builder` instance to apply the `RequestFilter` to.
     * @param RequestFilter $filter - The `RequestFilter` that should be applied.
     *
     * @return Builder - The modified `Builder` instance.
     */
    public function scopeFilter(Builder $query, RequestFilter $filter): Builder
    {
        return $filter->apply($query);
    }
}
