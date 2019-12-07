<?php

namespace Bluewing\Schema;

use Illuminate\Database\Schema\Blueprint as BaseBlueprint;

/**
 * Class Blueprint
 *
 * @package Bluewing\Schema
 */
class Blueprint extends BaseBlueprint
{
    use ManagesModelComments, PrefersCamelCaseColumnNames;
}
