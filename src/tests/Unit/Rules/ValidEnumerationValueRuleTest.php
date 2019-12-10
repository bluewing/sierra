<?php

namespace Tests\Unit\Rules;

use Bluewing\Rules\ValidEnumerationValueRule;
use Bluewing\Enumerations\OrganizationPreference;
use PHPUnit\Framework\TestCase;

final class ValidEnumerationValueRuleTest extends TestCase
{
    /**
     * @var ValidEnumerationValueRule
     */
    protected ValidEnumerationValueRule $rule;

    /**
     * Configures the test cases by instantiating the validation rule.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->rule = new ValidEnumerationValueRule(OrganizationPreference::class);
    }

    /**
     * Ensures that the validation rule will return `true` if the provided value exists in the enumeration.
     *
     * @return void
     */
    public function test_passes_with_valid_value()
    {
        $this->assertTrue($this->rule->passes('test', 1));
    }

    /**
     * Ensures that the validation rule will return `false` if the provided value does not exist in the enumeration.
     * The provided value of 10 does not exist in the `OrganizationPreference` enumeration.
     *
     * @return void
     */
    public function test_fails_if_value_out_of_enumeration_range()
    {
        $this->assertFalse($this->rule->passes('test', 10));
    }
}
