<?php

namespace Bluewing\Rules\Support;

use Exception;
use Illuminate\Contracts\Validation\Rule;

class RuleMissingDefaultMessageMethod extends Exception
{
    /**
     * If a `Rule` which includes the `HasCustomizableMessage` trait does not have a provided `defaultMessage`
     * method, then this exception will be thrown.
     *
     * @param Rule $customRule - The `Rule` which is missing the `defaultMessage` method.
     *
     * @return RuleMissingDefaultMessageMethod - The returned instance of the `RuleMissingDefaultMessageMethod`
     * exception.
     */
    public static function forRule(Rule $customRule): RuleMissingDefaultMessageMethod
    {
        $customRuleClass = get_class($customRule);
        return new RuleMissingDefaultMessageMethod("Please provide a defaultMessage method for $customRuleClass");
    }
}