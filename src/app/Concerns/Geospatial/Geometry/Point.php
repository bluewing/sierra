<?php

namespace Bluewing\Concerns\Geospatial\Geometry;

class Point extends BaseGeometry implements GeometryInterface
{
    /**
     * Defines a `Point`, one of the simplest Geometry types in GIS, consisting of two coordinate components, an x value
     * (longitude) and a y value (latitude).
     *
     * @see https://datatracker.ietf.org/doc/html/rfc7946#section-3.1.2
     *
     * @param float $x - The x-coordinate of the Point. When dealing with geospatial data, this represents the longitude.
     * @param float $y - The y-coordinate of the Point. When dealing with geospatial data, this represents the latitude.
     */
    public function __construct(protected float $x, protected float $y) {}

    /**
     * Converts the `Point` object to the Well-Known Text (WKT) representation of a point.
     *
     * @return string - The WKT representation of this point.
     */
    public function toWKT(): string
    {
        return "POINT($this->x $this->y)";
    }

    /**
     * Creates a `Point` object from the Well-Known Text (WKT) representation of a point.
     *
     * @param string $wkt - The WKT string.
     *
     * @return static - A newly-created instance of a `Point` from the provided `WKT`.
     */
    public static function fromWKT(string $wkt): static
    {
        preg_match_all("/POINT\(([0-9.]+) ([0-9.]+)\)/", $wkt, $matches);
        return (new Point((float) $matches[1][0], (float) $matches[2][0]));
    }

    /**
     * Creates a `Point` object from the GeoJSON representation of a point.
     *
     * @param $geojson - The GeoJSON structure.
     *
     * @return static - A newly-created instance of a `Point` from the provided GeoJSON snippet.
     */
    public static function fromGeoJSON($geojson): static
    {
        return new Point((float)$geojson['coordinates'][0], (float)$geojson['coordinates'][1]);
    }

    /**
     * Serializes the `Point` as a valid GeoJSON `Point` consisting of a single [lon, lat] coordinate. This method is
     * also used by the `Arrayable` interface's `toArray()` method—which is defined in `BaseGeometry`.
     *
     * @see https://laravel.com/docs/9.x/eloquent-mutators#array-json-serialization
     *
     * @return array - The `Point` object returned as an `array`, ready to be serialized to JSON.
     */
    public function jsonSerialize(): array
    {
        return [
            'type' => 'Point',
            'coordinates' => [$this->x, $this->y]
        ];
    }
}
