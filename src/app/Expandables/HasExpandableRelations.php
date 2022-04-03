<?php

namespace Bluewing\Expandables;

interface HasExpandableRelations
{
    /**
     * Fetches the associated `Expandables` class for the current Eloquent model.
     */
    public function expandableInstance();
    public function expandableRelations();
}
