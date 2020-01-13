<?php

namespace Bluewing\Contracts;

interface HasExpandableRelations
{
    /**
     * An array of strings containing the acceptable relations that may be returned from queries involving this model.
     *
     * @return array
     */
    public function relationsWhitelist(): array;
}
