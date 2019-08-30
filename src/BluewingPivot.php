<?php

namespace Bluewing\SharedServer;

use Illuminate\Database\Eloquent\Relations\Pivot;

class BluewingPivot extends Pivot
{
    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

    public static $snakeAttributes = false;

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';
}
