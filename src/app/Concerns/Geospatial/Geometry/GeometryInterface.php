<?php

namespace Bluewing\Concerns\Geospatial\Geometry;

use Bluewing\Concerns\Geospatial\Enumerations\Srid;

interface GeometryInterface
{
    public function setSrid(Srid $srid): self;
    public function getSrid(): Srid;
    public function toWKT(): string;
    public static function fromWKT(string $wkt): static;
    public static function fromWKB(string $wkb): static;
    public static function fromGeoJSON($geojson): static;
}
