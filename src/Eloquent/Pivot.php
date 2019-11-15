<?php

namespace Bluewing\Eloquent;

use Illuminate\Database\Eloquent\Relations\Pivot as EloquentPivot;
use Bluewing\Eloquent\UsesUuid;
use Bluewing\Eloquent\UsesCamelCaseAttrributes;

/**
 * Pivot class designed to set a few properties that Bluewing models utilise.
 */
abstract class Pivot extends EloquentPivot
{
    use UsesUuid, UsesCamelCaseAttributes;
}
