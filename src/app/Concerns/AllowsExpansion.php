<?php


namespace Bluewing\Concerns;

use Bluewing\Contracts\HasExpandableRelations;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use ReflectionClass;
use ReflectionException;

/**
 * A trait which provides the functionality of the `expands` scope to traited models.
 *
 * @package Bluewing\Concerns
 */
trait AllowsExpansion
{
    /**
     * When a model that `AllowsExpansion` is booted, check to see if the request includes an `expand` parameter, if
     * true, add the appropriate query to retrieve the requested relations.
     *
     * @param Builder $query - The `Builder` associated with the query.
     *
     * @return Builder - The modified `Builder` containing the relations to retrieve.
     *
     * @throws ReflectionException - A `ReflectionException` will be thrown if the traited class cannot be reflected.
     */
    public function scopeExpands(Builder $query): Builder
    {
        if (!request()->has('expand')) return $query;

        $reflect = new ReflectionClass($query->getModel());
        if (!$reflect->implementsInterface(HasExpandableRelations::class)){
            return $query;
        }

        $relationsToGet = Arr::wrap(request()->query('expand'));
        $invalidRelations = array_diff($relationsToGet, $query->getModel()->relationsWhitelist());

        if (!empty($invalidRelations)) {
            abort(422, 'Invalid model relation requested.');
        }

        return $query->with($relationsToGet);
    }
}
