<?php


namespace Bluewing\Concerns;

use Bluewing\Contracts\HasExpandableRelations;
use Bluewing\Scopes\ExpandableScope;
use Illuminate\Database\Eloquent\Scope;
use ReflectionClass;
use ReflectionException;

/**
 * A trait which provides the functionality of the `expands` scope to traited models.
 *
 * @package Bluewing\Concerns
 */
trait AllowsExpansion
{

    /**
     * When a model that `AllowsExpansion` is booted, check to see if the request includes an `expand` parameter, if
     * true, add the appropriate global scope to retrieve the
     *
     * @return void
     *
     * @throws ReflectionException - A `ReflectionException` will be thrown if the traited class cannot be reflected.
     */
    protected static function bootAllowsExpansion()
    {
        if (!request()->has('expand')) return;

        $reflect = new ReflectionClass(static::class);
        if (!$reflect->implementsInterface(HasExpandableRelations::class)) return;

        static::addGlobalScope(new ExpandableScope);
    }

    /**
     * @param Scope $scope
     *
     * @return mixed
     */
    public static abstract function addGlobalScope(Scope $scope);
}
