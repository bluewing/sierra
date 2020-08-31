<?php


namespace Bluewing\Concerns;

use Bluewing\Contracts\HasExpandableRelations;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model as EloquentModel;
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
     * Retrieves the `Relations` instance for the current model.
     *
     * @return mixed - An instance of the `Relations` class associated with the Eloquent model that is being fetched.
     *
     * @throws ReflectionException - Reflection is needed to compute the short name of the Eloquent class to match
     * with the associated `Relations` class. If this is unable to be completed, a `ReflectionException` will be thrown.
     */
    public function expandableRelations()
    {
        $className = (new ReflectionClass($this))->getShortName();
        $relationsClassName = config('bluewing.relations.namespace') . '\\' . $className . 'Relations';

        return new $relationsClassName();
    }

    /**
     * Fetches the valid expandable relations for the primary model, as requested by the user.
     *
     * @return array - An `array` of expandable relations that can be safely retrieved from the database alongside
     * the primary model.
     *
     * @throws ReflectionException - Reflection is needed to compute the short name of the Eloquent class to match
     * with the associated `Relations` class. If this is unable to be completed, a `ReflectionException` will be thrown.
     */
    public function getExpandableRelations(): array
    {
        if (!request()->has('expand')) return [];

        $requestedExpansions = Arr::wrap(request()->query('expand'));
        $expansions = [];

        foreach ($requestedExpansions as $requestedExpansion) {
            if ($this->checkExpansionIsValid($requestedExpansion)) {
                $expansions[] = $requestedExpansion;
            }
        }

        return $expansions;
    }

    /**
     * Evaluates whether a single expansion query parameter is valid. The expansion is first exploded into an array of
     * individual expansion segments. If more than four segments make up the expansion, it is considered invalid.
     * Each segment of the expansion is validated in turn by instantiating an instance of the model associated with
     * the expansion segment, and then checking if the next segment is a valid expansion for the previous segment.
     *
     * @param string $expansion - The expansion to check for validity.
     *
     * @return bool - `true` if the expansion request is valid, `false` otherwise.
     *
     * @throws ReflectionException - Reflection is needed to compute the short name of the Eloquent class to match
     * with the associated `Relations` class. If this is unable to be completed, a `ReflectionException` will be thrown.
     */
    private function checkExpansionIsValid(string $expansion): bool
    {
        $expansionArray = explode('.', $expansion);

        if (count($expansionArray) > 4) {
            return false;
        }

        $model = $this;

        foreach ($expansionArray as $expansionString) {
            if (!($model instanceof HasExpandableRelations)) {
                return false;
            }

            if (in_array($expansionString, array_keys($model->expandableRelations()->always()))) {
                $nextClass = $model->expandableRelations()->always()[$expansionString];
                $model = new $nextClass();

                continue;
            }

            if (method_exists($model, $expansionString)) {
                list('model' => $nextClass, 'isAuthorized' => $authorizationFn) = $model->expandableRelations()
                    ->{$expansionString}();

                if (!$authorizationFn()) {
                    return false;
                }

                $model = new $nextClass();
                continue;
            }

            return false;
        }

        return true;
    }

    /**
     * When a model that `AllowsExpansion` is booted, check to see if the request includes an `expand` parameter, if
     * true, add the appropriate query to retrieve the requested relations. This scope should be used as needed,
     * except for when models are retrieved via route model binding.
     *
     * @param Builder $query - The `Builder` associated with the query.
     *
     * @return Builder - The modified `Builder` containing the relations to retrieve.
     *
     * @throws ReflectionException - Reflection is needed to compute the short name of the Eloquent class to match
     * with the associated `Relations` class. If this is unable to be completed, a `ReflectionException` will be thrown.
     */
    public function scopeExpands(Builder $query): Builder
    {
        $expandableRelations = $this->getExpandableRelations();

        if (empty($expandableRelations) || !method_exists($query->getModel(), 'expandableRelations')) {
            return $query;
        }

        return $query->with($expandableRelations);
    }

    /**
     * Override the routing binding resolution to explicitly capture any expandable objects requested, by binding to
     * the local `expands` scope defined in `AllowsExpansion` trait.
     *
     * @see UrlRoutable
     *
     * @param  mixed  $value - The key value to retrieve.
     * @param  string|null  $field - The field to retrieve the model by.
     *
     * @return EloquentModel|null
     */
    public function resolveRouteBinding($value, $field = null)
    {
        return $this->expands()->where($this->getRouteKeyName(), $value)->first();
    }
}
