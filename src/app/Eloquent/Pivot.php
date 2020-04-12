<?php

namespace Bluewing\Eloquent;

use Bluewing\Eloquent\Model as BluewingModel;
use Illuminate\Database\Eloquent\Relations\Concerns\AsPivot;

/**
 * Pivot class designed to set a few properties that Bluewing models utilise.
 */
abstract class Pivot extends BluewingModel
{
    use AsPivot;
}
