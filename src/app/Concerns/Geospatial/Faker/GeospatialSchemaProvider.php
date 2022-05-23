<?php

namespace Bluewing\Concerns\Geospatial\Faker;

use Faker\Provider\Base as FakerProviderBase;

class GeospatialSchemaProvider extends FakerProviderBase
{
    /**
     * Supplies a `fakerphp` provider that can generate a GeoJSON standards compliant `Point` object consisting of
     * a schema and a single coordinate.
     *
     * @return array - An associative array that contains a representation of a GeoJSON `Point`.
     */
    public function geoJsonPoint(): array
    {
        return [
            'type'          => 'Point',
            'coordinates'   => [$this->generator->longitude, $this->generator->latitude]
        ];
    }
}
