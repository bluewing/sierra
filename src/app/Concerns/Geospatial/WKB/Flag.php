<?php

namespace Bluewing\Concerns\Geospatial\WKB;

/**
 * Defines the flags that encode additional information in an EWKB.
 */
enum Flag: int
{
    // https://github.com/postgis/postgis/blob/master/liblwgeom/liblwgeom.h.in#L140
    // decimal 536870912
    case SRID = 0x20000000;
}
