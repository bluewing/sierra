<?php

namespace Bluewing\Concerns\Geospatial\WKB;

/**
 * Defines the available GeometryType's available according to the OpenGIS specification.
 *
 * @see https://postgis.net/docs/using_postgis_dbmanagement.html
 * @see https://www.ogc.org/standards/sfa, Page 63, "OpenGIS Implementation Specification for Geographic information -
 * Simple feature access - Part 1: Common architecture"
 */
enum WKBGeometryType: int
{
    case Geometry = 0;
    case Point = 1;
    case LineString = 2;
    case Polygon = 3;
    case MultiPoint = 4;
    case MultiLineString = 5;
    case MultiPolygon = 6;
    case GeometryCollection = 7;
    case CircularString = 8;
    case CompoundCurve = 9;
    case CurvePolygon = 10;
    case MultiCurve = 11;
    case MultiSurface = 12;
    case Curve = 13;
    case Surface = 14;
    case PolyhedralSurface = 15;
    case TriangulatedIrregularNetwork = 16;
}
