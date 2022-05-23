<?php

namespace Bluewing\Concerns\Geospatial\Enumerations;

/**
 * Defines an enumeration of commonly used spatial reference identifiers for the purposes of storing geospatial
 * information.
 *
 * @see https://spatialreference.org/ref/epsg/4326/
 */
enum Srid: int
{
    case None = 0;
    case WGS84 = 4326;
}
