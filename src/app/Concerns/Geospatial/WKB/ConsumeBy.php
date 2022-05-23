<?php

namespace Bluewing\Concerns\Geospatial\WKB;

/**
 * An enumeration expressing the allowed increments that a `Consumable` can be consumed in.
 */
enum ConsumeBy: int
{
    case Byte = 1;
    case Integer = 4;
    case Double = 8;
}
