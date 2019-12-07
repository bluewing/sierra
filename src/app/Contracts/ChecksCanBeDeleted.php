<?php


namespace Bluewing\Contracts;


interface ChecksCanBeDeleted
{
    /**
     * Helper function to check if the entity can be deleted.
     *
     * @return bool - `true` if the entity can be deleted.
     */
    public function canBeDeleted(): bool;
}
