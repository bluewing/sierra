<?php

namespace Bluewing\Eloquent;

/**
 * Pivot class designed to set a few properties that Bluewing models utilise.
 */
abstract class Pivot extends Model
{
    /**
     * The name of the "created at" column.
     *
     * @var string
     */
    const CREATED_AT = 'createdAt';

    /**
     * The name of the "updated at" column.
     *
     * @var string
     */
    const UPDATED_AT = 'updatedAt';
}
