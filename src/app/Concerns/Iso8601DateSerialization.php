<?php


namespace Bluewing\Concerns;


use Carbon\Carbon;
use DateTimeInterface;

trait Iso8601DateSerialization
{
    /**
     * Prepare a date for array / JSON serialization by casting it to an ISO8601 string.
     *
     * @param  DateTimeInterface  $date
     *
     * @return string
     */
    protected function serializeDate(DateTimeInterface $date)
    {
        return Carbon::instance($date)->toIso8601String();
    }
}
