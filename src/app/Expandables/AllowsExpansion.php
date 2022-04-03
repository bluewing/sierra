<?php


namespace Bluewing\Expandables;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Support\Arr;
use ReflectionException;

/**
 * A trait which provides the functionality of the `expands` scope to traited models.
 *
 * @package Bluewing\Expandables
 */
trait AllowsExpansion
{
    /**
     * @var string - The query string parameter from which expandable requests are extracted.
     */
    private string $expandQueryParameter = 'expand';

    /**
     * Retrieves an `ExpandablesInterface` instance for the current model that defines the current expandables for
     * the model and whether the user performing the expansion request is authorized to do so.
     *
     * @return BaseExpandables - An instance of the `BaseExpandables` associated with the Eloquent model that
     * is being fetched.
     *
     * @throws ReflectionException - Reflection is needed to compute the short name of the Eloquent class to match
     * with the associated `ExpandablesInterface` class. If this is unable to be completed, a `ReflectionException` will
     * be thrown.
     */
    public function expandableInstance(): BaseExpandables
    {
        $relationClassName = config('bluewing.relations.namespace') . '\\' . getShortName(self::class) . 'Expandables';
        return new $relationClassName;
    }

    /**
     * Fetches the valid expandables for the primary model, as requested by the user. If no `expand` parameter is
     * present in the request, return an empty `array`.
     *
     * @return array - An `array` of expandable relations that can be safely retrieved from the database alongside
     * the primary model.
     *
     * @throws ReflectionException - Reflection is needed to compute the short name of the Eloquent class to match
     * with the associated `Relations` class. If this is unable to be completed, a `ReflectionException` will be thrown.
     */
    public function expandableRelations(): array
    {
        if (request()->missing($this->expandQueryParameter)) {
            return [];
        }

        return array_filter(
            Arr::wrap(request()->query($this->expandQueryParameter)),
            fn($re) => $this->isExpansionValid($re)
        );
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
    private function isExpansionValid(string $expansion): bool
    {
        $expansionArray = explode('.', $expansion);

        // Precondition preventing the expansion array from being more than four nested expansions deep.
        if (count($expansionArray) > 4) {
            return false;
        }

        $model = $this;

        foreach ($expansionArray as $expansionComponent) {
            // Each database model must itself implement `HasExpandableRelations`.
            if (!($model instanceof HasExpandableRelations)) {
                return false;
            }
            // If the expansion component is present in the current model's expandable instance, then the expansion
            // is allowed. Internally, this checks both for the presence of the model in the `always` method, as well
            // as checking if the expansion is its own method which makes a static check over whether authorization to
            // the expansion is allowed.
            $model = $model->expandableInstance()->getExpandableModel($expansionComponent);

            if (!is_null($model)) {
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
        $expandableRelations = $this->expandableRelations();

        if (empty($expandableRelations) || !method_exists($query->getModel(), 'expandableInstance')) {
            return $query;
        }

        return $query->with($expandableRelations);
    }

    /**
     * Override the routing binding resolution to explicitly capture any expandable objects requested, by binding to
     * the local `expands` scope defined in this `AllowsExpansion` trait.
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
