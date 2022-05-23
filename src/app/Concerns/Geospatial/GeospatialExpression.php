<?php

namespace Bluewing\Concerns\Geospatial;

use Bluewing\Concerns\Geospatial\Geometry\BaseGeometry;
use Illuminate\Database\Query\Expression;

/**
 * @property BaseGeometry $value
 */
class GeospatialExpression extends Expression
{
    /**
     * Returns the `GeospatialExpression` as a string value which involves calling the postgis `ST_GeomFromText`
     * function.
     *
     * @see https://postgis.net/docs/ST_GeomFromText.html
     *
     * @return string - The value of an expression, evaluating to a call of the postgis `ST_GeomFromText` function.
     */
    public function getValue()
    {
        return "ST_GeomFromText(?, ?)";
    }

    /**
     * Gets the `BaseGeometry` instance that is backing this `GeospatialExpression` instance.
     *
     * @return BaseGeometry - The `BaseGeometry` instance.
     */
    public function getGeometry()
    {
        return $this->value;
    }

    /**
     * Retrieves the spatial value for the `GeospatialExpression`, which involves returning the WKT representation
     * of the `BaseGeometry` type.
     *
     * @return string - The WKT of the `BaseGeometry` that is being expressed.
     */
    public function getSpatialValue(): string
    {
        return $this->value->toWKT();
    }

    /**
     * Retrieves the SRID of the `BaseGeometry` type. This is the second parameter in the `ST_GeomFromText` function.
     *
     * @return int - The SRID expressed as an integer.
     */
    public function getSrid(): int
    {
        return $this->value->getSrid()->value;
    }
}
