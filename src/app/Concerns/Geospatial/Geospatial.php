<?php

namespace Bluewing\Concerns\Geospatial;

use Bluewing\Concerns\Geospatial\Geometry\BaseGeometry;
use Exception;

trait Geospatial
{
    /**
     * Models using the `Geospatial` trait will use the `GeospatialQueryBuilder` as the query builder instance, which
     * provides the functionality to bind `GeospatialExpression` values correctly.
     *
     * @return GeospatialQueryBuilder - An instance of `GeospatialQueryBuilder`.
     */
    protected function newBaseQueryBuilder(): GeospatialQueryBuilder
    {
        return new GeospatialQueryBuilder(
            $this->getConnection(),
            $this->getConnection()->getQueryGrammar(),
            $this->getConnection()->getPostProcessor()
        );
    }

    /**
     * Overrides the `setRawAttributes` method in the `HasAttributes` trait to cast any attributes geospatially.
     *
     * @param array $attributes - The `array` of attributes to be cast.
     * @param $sync - Boolean flag indicating if the attributes should be synced. Calls `syncOriginal` inside
     * `parent::setRawAttributes()`.
     *
     * @throws Exception - An `Exception` will be thrown if the WKB cannot be parsed, or results in parsing to an
     * unsupported geometry type.
     */
    public function setRawAttributes(array $attributes, $sync = false)
    {
        $geospatialCasts = $this->getGeospatialCasts();

        foreach ($attributes as $attribute => &$value) {
            if ($this->doesAttributeNeedCastingFromWKB($attribute, $geospatialCasts, $value)) {
                $value = BaseGeometry::fromWKB($value);
            }
        }

        parent::setRawAttributes($attributes, $sync);
    }

    /**
     * Returns an `array` of the attributes of the `Geospatial` model that have casts that involve `BaseGeometry`.
     * These are then used to modify the attribute value into a `BaseGeometry` instance.
     *
     * @return string[] - An `array` of the attributes that are cast geospatially.
     */
    private function getGeospatialCasts(): array
    {
        return collect($this->getCasts())
            ->filter(fn($c) => is_subclass_of($c, BaseGeometry::class))
            ->keys()
            ->toArray();
    }

    /**
     * An attribute will need casting from a WKB if it is listed as being cast via a `BaseGeometry` cast, and is
     * not already a `BaseGeometry` instance.
     *
     * @param string $attribute - The attribute being cast.
     * @param array $geospatialCasts - An `array` of the geospatially cast attributes.
     * @param BaseGeometry|string|null $value - The value that may need casting,
     *
     * @return bool - `true` if the attribute needs casting from an EWKB string, `false` if it is not listed as being
     * geospatially cast, or has already been cast as such.
     */
    private function doesAttributeNeedCastingFromWKB(string $attribute, array $geospatialCasts,
                                                     BaseGeometry|string|null $value): bool
    {
        return in_array($attribute, $geospatialCasts) && is_string($value);
    }
}
