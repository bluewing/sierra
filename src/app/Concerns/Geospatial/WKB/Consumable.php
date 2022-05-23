<?php

namespace Bluewing\Concerns\Geospatial\WKB;

use RuntimeException;

class Consumable
{
    /**
     * @var string - The binary EWKB data stored as a string.
     */
    protected string $ewkbData;

    /**
     * @var int - A non-negative integer expressing the current position up to where the EWKB string has been consumed.
     */
    protected int $position = 0;

    /**
     * @var int - The length of the binary data.
     */
    protected int $dataLength;

    /**
     * Provides a helpful contract that allows for controlled, forward scanning through the EWKB in valid increments.
     *
     * @param string $hexEncodedEwkbString - The EWKB data stored as a string of hexadecimal characters.
     */
    public function __construct(string $hexEncodedEwkbString) {
        $this->ewkbData     = pack('H*', $hexEncodedEwkbString);
        $this->dataLength   = strlen($this->ewkbData);
    }

    /**
     * Consumes a single byte from the EWKB data by unpacking an unsigned char from the EWKB.
     *
     * @return int - The integer that was unpacked from the EWKB.
     */
    public function consumeByte(): int
    {
        return unpack('C', $this->consume(ConsumeBy::Byte))[1];
    }

    /**
     * Consumes four bytes from the EWKB data by unpacking an unsigned long from the EWKB.
     *
     * @param bool $isLittleEndian - Flag indicating whether the EWKB is stored in little endian form. This identifies
     * which flag to use in the `unpack` function to retrieve the bytes.
     *
     * @return int - The integer that was unpacked from the EWKB.
     */
    public function consumeInteger(bool $isLittleEndian): int
    {
        return unpack($isLittleEndian ? 'V' : 'N', $this->consume(ConsumeBy::Integer))[1];
    }

    /**
     * Consumes eight bytes from the EWKB data by unpacking a double from the EWKB.
     *
     * @param bool $isLittleEndian - Flag indicating whether the EWKB is stored in little endian form. This identifies
     * which flag to use in the `unpack` function to retrieve the bytes.
     *
     * @return float - The float that was unpacked from the EWKB.
     */
    public function consumeDouble(bool $isLittleEndian): float
    {
        $str = $this->consume(ConsumeBy::Double);

        if (!$isLittleEndian) {
            $str = strrev($str);
        }

        return unpack('d', $str)[1];
    }

    /**
     * Consumes a portion of the EWKB's contents by the provided `ConsumeBy` enumeration in a sequential manner by
     * permanently incrementing the current position that has been read from the EWKB on each consumption.
     *
     * @param ConsumeBy $consumeBy - The amount of the EWKB to consume.
     *
     * @return string - The value retrieved from the EWKB as a byte string.
     */
    private function consume(ConsumeBy $consumeBy): string
    {
        if ($this->position + $consumeBy->value > $this->dataLength) {
            throw new RuntimeException("$consumeBy->name exceeds remaining length of EWKB.");
        }
        $value = substr($this->ewkbData, $this->position, $consumeBy->value);
        $this->position += $consumeBy->value;;
        return $value;
    }
}
