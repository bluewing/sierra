<?php

namespace Bluewing\Concerns;

use Carbon\Carbon;
use DateTimeInterface;

trait SerializesDatesToIso8601
{
    /**
     * Prepare a date for array/JSON serialization by casting it to an ISO8601-conforming string. This overrides the
     * Eloquent `Model` class definition for `serializeDate`.
     *
     * @param DateTimeInterface $date
     *
     * @return string - An ISO8601 string representing the date stored in the database.
     */
    protected function serializeDate(DateTimeInterface $date)
    {
        return Carbon::instance($date)->toIso8601String();
    }
}
