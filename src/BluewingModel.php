<?php

namespace Bluewing;

use Illuminate\Database\Eloquent\Model;

abstract class BluewingModel extends Model {

    /**
     *
     */
    public static $snakeAttributes = false;

    /**
     *
     */
    const CREATED_AT = 'createdAt';

    /**
     *
     */
    const UPDATED_AT = 'updatedAt';
}
