<?php

namespace Bluewing\Concerns\Geospatial\Geometry;

use Bluewing\Concerns\Geospatial\Enumerations\Srid;
use Bluewing\Concerns\Geospatial\GeospatialExpression;
use Bluewing\Concerns\Geospatial\WKB\Consumer;
use Illuminate\Contracts\Database\Eloquent\Castable;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Exception;
use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;

abstract class BaseGeometry implements GeometryInterface, Castable, JsonSerializable, Arrayable
{
    /**
     * @var Srid - The SRID of the `GeometryInterface` object. This default to `0`, but will usually be set to WGS84.
     */
    protected Srid $srid = Srid::None;

    /**
     * Sets the SRID of the `BaseGeometry` object and returns itself, allowing for fluent chaining and returns.
     *
     * @param Srid $srid - The SRID enumeration to set for the `BaseGeometry`.
     *
     * @return $this - Returns itself, allowing for fluent chaining.
     */
    public function setSrid(Srid $srid): self
    {
        $this->srid = $srid;
        return $this;
    }

    /**
     * Accessor for the `BaseGeometry`'s SRID.
     *
     * @return Srid - The SRID that has been set for the `BaseGeometry`.
     */
    public function getSrid(): Srid
    {
        return $this->srid;
    }

    /**
     * Fetches the `BaseGeometry` as an `Expression` for the purposes of being inserted into a PostgreSQL database via
     * Eloquent.
     *
     * @return GeospatialExpression - The subclass of `Expression` that handles how the `BaseGeometry` is parsed for the
     * purposes of database insertion.
     */
    public function asExpression(): GeospatialExpression
    {
        return new GeospatialExpression($this);
    }

    /**
     * From a WKB hex-encoded string, produce a `BaseGeometry` object. This internally utilises our own custom WKB
     * consumption logic, which currently handles `Point`'s only.
     *
     * @param string $wkb - The WKB hex-encoded string that should be parsed into a `BaseGeometry` object.
     *
     * @return static - An instance of a `BaseGeometry` object from the parsed hex-encoded WKB string.
     *
     * @throws Exception - An `Exception` will be thrown if the WKB cannot be parsed, or results in parsing to an
     * unsupported geometry type.
     */
    public static function fromWKB(string $wkb): static
    {
        return (new Consumer)->parse($wkb);
    }

    /**
     * The presence of this method fulfills the `Castable` interface and allows `BaseGeometry` subclasses to be provided
     * as cast objects on Laravel models, i.e. `$casts = ['coordinate' => Point::class];`, even though `Point` itself
     * does not implement `CastAttributes` directly.
     *
     * @param array $arguments - An array of arguments to provide to the `CastsAttributes` contractor.
     *
     * @return CastsAttributes - An anonymous PHP class which can be used to cast attributes to a `GeometryInterface`.
     */
    public static function castUsing(array $arguments)
    {
        return new class(static::class) implements CastsAttributes
        {
            /**
             * @param string $className - The class name of the `BaseGeometry` object.
             */
            public function __construct(protected string $className) {}

            /**
             * Gets the attribute as a `BaseGeometry` object, or parses it from a `WKT` string representation (this is
             * usually not necessary as it will have been cast to a `BaseGeometry` object via
             * `Geospatial::setRawAttributes`.
             *
             * @param $model - The `Model` the cast is acting on.
             * @param string $key - The key of the attribute that is being cast.
             * @param $value - The value of the attribute that is being cast.
             * @param array $attributes - An array of the attributes of the `Model`.
             *
             * @return GeometryInterface - The cast attribute as a `GeometryInterface`.
             */
            public function get($model, string $key, $value, array $attributes)
            {
                if (is_null($value)) {
                    return null;

                } else if ($value instanceof BaseGeometry) {
                    return $value;

                } else if ($value instanceof GeospatialExpression) {
                    return $value->getGeometry();
                }

                return $this->className::fromWKT($value)->setSrid(Srid::WGS84);
            }

            /**
             * Sets the attribute as a `GeospatialExpression` ready for insertion into the database. This involves
             * converting the value to a `BaseGeometry` object (if it isn't already one) by parsing the current value
             * as an (expected) GeoJSON object, before returning the `BaseGeometry` object as a `GeospatialExpression`.
             *
             * @param $model - The `Model` the cast is acting on.
             * @param string $key - The key of the attribute that is being cast.
             * @param $value - The value of the attribute that is being cast.
             * @param array $attributes - An array of the attributes of the `Model`.
             *
             * @return GeospatialExpression|null - The cast attribute as a `GeospatialExpression`, or `null`.
             */
            public function set($model, string $key, $value, array $attributes): ?GeospatialExpression
            {
                if (is_null($value)) {
                    return null;
                } else if (!$value instanceof BaseGeometry) {
                    $value = $this->className::fromGeoJSON($value)->setSrid(Srid::WGS84);
                }
                return $value->asExpression();
            }
        };
    }

    /**
     * Utilizes a `BaseGeometry` subclass' `jsonSerialize` method to convert the `BaseGeometry` value object to an
     * GeoJSON-conforming `array`/JSON structure.
     *
     * @see https://laravel.com/docs/9.x/eloquent-mutators#array-json-serialization
     *
     *  @return array - The GeoJSON-conforming representation of the `BaseGeometry` value object.
     */
    public function toArray()
    {
        return $this->jsonSerialize();
    }
}
