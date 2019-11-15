<?php

namespace Bluewing\Eloquent;

/**
 *
 */
trait UsesUuid
{
  /**
   * We define our own primary keys as UUIDs, so no need for autoincrementing
   * functonality.
   */
  public $increments = false;

  /**
   *
   */
  public abstract function getKey();

  /**
   *
   */
  public abstract function getKeyName();

  /**
   * Boot function to ensure that models that utilize this trait use v4 UUIDs instead of
   * incrementing integers as primary keys.
   */
  protected static function bootUsingUuids()
  {
    static::creating(function($model) {
      if (!$model->getKey()) {
        $model->{$model->getKeyName()} = Str::uuid()->toString();
      }
    });
  }
}