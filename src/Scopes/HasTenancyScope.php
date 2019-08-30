<?php

namespace Bluewing\SharedServer\Scopes;

/**
 * A trait which provides the functionality of the `TenancyScope` scope to traited models.
 */
trait HasTenancyScope {
    protected static function boot() {
        parent::boot();
        static::addGlobalScope(new TenancyScope);
    }
}
