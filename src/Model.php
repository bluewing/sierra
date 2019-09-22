<?php

namespace Bluewing;

use Illuminate\Database\Eloquent\Model as EloquentModel;

abstract class Model extends EloquentModel {
    /**
     * Do not use snake_case for model attributes.
     */
    public static $snakeAttributes = false;

    /**
     * Ensure that the `CREATED_AT` timestamp field is formatted appropriately.
     */
    const CREATED_AT = 'createdAt';

    /**
     * Ensure that the `UPDATED_AT` timestamp field is formatted appropriately.
     */
    const UPDATED_AT = 'updatedAt';
}
