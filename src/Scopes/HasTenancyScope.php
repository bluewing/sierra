<?php

namespace Bluewing\Scopes;

/**
 * A trait which provides the functionality of the `TenancyScope` scope to traited models.
 */
trait HasTenancyScope {

    /**
     * @return void
     */
    protected static function boot() {
        parent::boot();
        static::addGlobalScope(new TenancyScope);
    }
}
