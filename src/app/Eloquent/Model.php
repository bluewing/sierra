<?php

namespace Bluewing\Eloquent;

use Bluewing\Concerns\SerializesDatesToIso8601;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Support\Str;

abstract class Model extends EloquentModel
{
    use SerializesDatesToIso8601;

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

    /**
     * Don't use snake_case.
     *
     * @var bool
     */
    public static $snakeAttributes = false;

    /**
     * We define our own primary keys as UUIDs, so no need for autoincrementing functionality.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The key type, being GUID, is a string.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Boot function to ensure that models that utilize this trait use v4 UUIDs instead of incrementing integers as
     * primary keys.
     */
    protected static function boot()
    {
        static::bootTraits();

        static::creating(function(EloquentModel $model) {
            if (!$model->getKey()) {
                $model->{$model->getKeyName()} = Str::uuid()->toString();
            }
        });
    }
}
