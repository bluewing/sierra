<?php

namespace Bluewing\Concerns\Geospatial\WKB;

use Bluewing\Concerns\Geospatial\Enumerations\Srid;
use Bluewing\Concerns\Geospatial\Geometry\GeometryInterface;
use Bluewing\Concerns\Geospatial\Geometry\Point;
use Exception;
use RuntimeException;

class Consumer
{
    /**
     * @var Consumable - The EWKB hexadecimal string stored as a consumable which can be paged through by units of
     * `ConsumeBy` enumerations.
     */
    protected Consumable $consumable;

    /**
     * @var bool - A flag indicating the endianness of the stored EWKB.
     */
    protected bool $isLittleEndian;

    /**
     * @var Srid - The SRID that has been parsed from the EWKB. Defaults to None, but will be expected to be parsed as
     * WGS84, most likely.
     */
    protected Srid $srid = Srid::None;

    /**
     * @var WKBGeometryType|null - The geometry type that is being parsed from the EWKB.
     */
    protected ?WKBGeometryType $type = null;

    /**
     * Parses the hex-encoded EWKB string provided to the class by consuming it in portions conforming to the EWKB
     * format, returning a `GeometryInterface` instance.
     *
     * @param string $hexEncodedEwkbString - The EWKB string that has been encoded with hexadecimal characters.
     *
     * @return GeometryInterface - The created `GeometryInterface` object that was discerned from the EWKB. For now,
     * this will only be `Point` objects, as it is the only implemented `GeometryType`.
     *
     * @throws Exception - An `Exception` will be thrown if the matched `GeometryType` has not yet been implemented.
     */
    public function parse(string $hexEncodedEwkbString): GeometryInterface
    {
        $this->consumable = new Consumable($hexEncodedEwkbString);
        $this->parseEndianness();
        $this->parseType();

        // For now, we just assume the type is of point.
        return match($this->type) {
            WKBGeometryType::Point      => $this->parsePoint(),
            default                     => throw new Exception('GeometryType not implemented.')
        };
    }

    /**
     * The first byte of the EWKB provides information on the endianness of the remainder of the EWKB value.
     */
    private function parseEndianness(): void
    {
        $this->isLittleEndian = match ($this->consumable->consumeByte()) {
            0 => false,
            1 => true,
            default => throw new RuntimeException('Unparseable endian byte value.')
        };
    }

    /**
     * The second component of the EWKB provides both the SRID value and `GeometryType` of what has been stored. By
     * performing a bitwise masking operation, it is possible to extract both the SRID and `GeometryType` from the EWKB.
     *
     * For example, the second EWKB component for a `GeometryType` of `Point` will be 536870913, when expressed as an
     * integer. When represented as hexadecimal:
     *
     * 0x20000001
     * 0x20000000 - The SRID flag value.
     * &=
     * 0x20000000
     *
     * From here, the SRID can be extracted, and the `GeometryType` can be determined.
     *
     * @see https://github.com/postgis/postgis/blob/master/liblwgeom/liblwgeom.h.in#L140 - For the definition of the
     * SRID flag.
     */
    private function parseType(): void
    {
        $type = $this->consumable->consumeInteger($this->isLittleEndian);

        // Confirm the presence of an SRID value mask in the EWKB, and then extract the `GeometryType`` by using a
        // bitwise AND against the negated value of the SRID flag.
        if ($type & Flag::SRID->value) {
            $this->srid = Srid::from($this->consumable->consumeInteger($this->isLittleEndian));
            $this->type = WKBGeometryType::from($type & ~Flag::SRID->value);
        }
    }

    /**
     * Parses the remainder of the EWKB as a `Point` object.
     *
     * @return Point - The created `Point` which was parsed from the EWKB.
     */
    private function parsePoint(): Point
    {
        return (new Point(
            $this->consumable->consumeDouble($this->isLittleEndian),
            $this->consumable->consumeDouble($this->isLittleEndian)
        ))->setSrid($this->srid);
    }
}
