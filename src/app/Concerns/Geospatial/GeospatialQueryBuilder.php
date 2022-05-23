<?php

namespace Bluewing\Concerns\Geospatial;

use Illuminate\Database\Query\Builder as QueryBuilder;

class GeospatialQueryBuilder extends QueryBuilder
{
    /**
     * Provides additional functionality on top of `QueryBuilder` to bind `GeospatialExpression` instances correctly.
     *
     * @param array $bindings - An `array` of bindings to bind.
     *
     * @return array - The cleaned bindings.
     */
    public function cleanBindings(array $bindings)
    {
        $spatialBindings = [];
        foreach ($bindings as &$binding) {
            if ($binding instanceof GeospatialExpression) {
                $spatialBindings[] = $binding->getSpatialValue();
                $spatialBindings[] = $binding->getSrid();
            } else {
                $spatialBindings[] = $binding;
            }
        }

        return parent::cleanBindings($spatialBindings);
    }
}
