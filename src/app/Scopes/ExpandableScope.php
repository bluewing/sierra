<?php

namespace Bluewing\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Arr;

class ExpandableScope implements Scope
{
    /**
     * Apply the expandable scope to the query. If the `expand` query parameter is present in the URL, and is valid,
     * the requested related models will be included in the query output.
     *
     * @param Builder $builder - An instance of the eloquent query `Builder` class.
     * @param Model $model - The `Model` the scope is being applied to.
     *
     * @return Builder - Pass back the instance of the `Builder`, with the scope applied.
     */
    public function apply(Builder $builder, Model $model)
    {
        $relationsToGet = Arr::wrap(request()->query('expand'));
        $invalidRelations = array_diff($relationsToGet, $model->relationsWhitelist());

        if (!empty($invalidRelations)) abort(422);

        return $builder->with($relationsToGet);
    }
}
