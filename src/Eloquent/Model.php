<?php

namespace Bluewing\Eloquent;

use Illuminate\Database\Eloquent\Model as EloquentModel;
use Bluewing\Eloquent\UsesUuid;
use Bluewing\Eloquent\UsesCamelCaseAttrributes;

/**
 *
 */
abstract class Model extends EloquentModel
{
    use UsesUuid, UsesCamelCaseAttributes;
}
