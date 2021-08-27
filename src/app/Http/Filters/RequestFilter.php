<?php


namespace Bluewing\Http\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

abstract class RequestFilter
{
    /**
     * @var Builder - The `Builder` instance which will be modified.
     */
    protected Builder $builder;

    /**
     * Constructor for the `RequestFilter` object. Requires a dependency-injected instance of the `Request` object on
     * which to act.
     *
     * @param Request $request - The `Request` object that is going to provide the necessary parameters to filter
     * database records by.
     */
    public function __construct(protected Request $request) {}

    /**
     * Applies all relevant filters for each query parameter as constraints on the `Builder` instance.
     *
     * @param Builder $builder - The `Builder` instance to apply the filtering functions to.
     *
     * @return Builder - The modified `Builder` instance.
     */
    public function apply(Builder $builder): Builder
    {
        $this->builder = $builder;

        foreach ($this->request->all() as $queryName => $queryValue) {
           if (method_exists($this, $queryName)) {
               call_user_func_array([$this, $queryName], [$queryValue]);
           }
        }

        return $this->builder;
    }
}
