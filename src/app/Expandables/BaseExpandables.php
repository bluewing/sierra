<?php

namespace Bluewing\Expandables;

use Illuminate\Database\Eloquent\Model;

abstract class BaseExpandables implements ExpandablesInterface
{
    /**
     * All implementors of the `ExpandablesInterface` must provide an `always` method which returns an associative
     * `array` of expandables which may always be expanded, regardless of any condition.
     *
     * @return array - An associative `array` of expandables which may always be expanded, regardless of any condition.
     */
    public abstract function always(): array;

    /**
     * Gets the `Model` associated with the expansion identifier provided, if allowed and/or authorized. If the expansion
     * is invalid, not allowed, or not authorized, `null` will be returned.
     *
     * @param string $expansionIdentifier - The component from an expandable query string that is aiding in the retrieval
     * of an expandable model.
     *
     * @return Model|null - The `Model` associated with the expansion identifier, if it exists or is authorized to be
     * retrieved. If the expansion is not allowed or authorized, `null` wil be returned.
     */
    public function getExpandableModel(string $expansionIdentifier): ?Model
    {
        if ($this->isExpansionAllowedAlways($expansionIdentifier)) {
            return new ($this->always()[$expansionIdentifier]);

        } else if ($this->isExpansionAllowedConditionally($expansionIdentifier)) {
            return new ($this->$expansionIdentifier());
        }
        return null;
    }

    /**
     * Checks if the provided expansion identifier is present in the associative `array` returned by tje `always` method.
     *
     * @param string $expansionIdentifier - The component from an expandable query string that is aiding in the retrieval
     * of an expandable model.
     *
     * @return bool - `true` if the expansion identifier is present in the `always` method.
     */
    private function isExpansionAllowedAlways(string $expansionIdentifier): bool
    {
        return in_array($expansionIdentifier, array_keys($this->always()));
    }

    /**
     * Checks if the provided expansion identifier exists as a method, and if that method also returns a string
     * representing the `Model`.
     *
     * @param string $expansionIdentifier - The component from an expandable query string that is aiding in the retrieval
     * of an expandable model.
     *
     * @return bool - `true` if the expansion identifier both exists as a method, and that method returns a string
     * representing the `Model`.
     */
    private function isExpansionAllowedConditionally(string $expansionIdentifier): bool
    {
        return method_exists($this, $expansionIdentifier) && !is_null($this->$expansionIdentifier());
    }
}