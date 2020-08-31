<?php

namespace Bluewing\Contracts;

interface HasExpandableRelations
{
    /**
     * Fetches the associated `Relations` class for the current Eloquent model.
     */
    public function expandableRelations();
}
