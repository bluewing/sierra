<?php

namespace Bluewing\Concerns\Geospatial\Enumerations;


/**
 * Defines the available `GeometryType`'s as set out in the GeoJSON specification.
 *
 * @see https://datatracker.ietf.org/doc/html/rfc7946#section-1.4
 */
enum GeometryType: string
{
    case Point = 'Point';
    case MultiPoint = 'MultiPoint';
    case LineString = 'LineString';
    case MultiLineString = 'MultiLineString';
    case Polygon = 'Polygon';
    case MultiPolygon = 'MultiPolygon';
    case GeometryCollection = 'GeometryCollection';
}
