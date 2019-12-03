<?php


namespace Bluewing\Scopes;

use Illuminate\Database\Eloquent\Scope;

/**
 * A trait which provides the functionality of the `ExpandableScope` to traited models.
 *
 * @package Bluewing\Scopes
 */
trait HasExpandableScope
{
    /**
     * @return void
     */
    protected static function bootHasExpandableScope()
    {
        static::addGlobalScope(new ExpandableScope);
    }

    /**
     * @param Scope $scope
     * @return mixed
     */
    public static abstract function addGlobalScope(Scope $scope);
}
