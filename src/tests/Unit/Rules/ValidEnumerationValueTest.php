<?php

namespace Tests\Unit\Rules;

use Bluewing\Rules\ValidEnumerationValue;
use Mockery;
use PHPUnit\Framework\TestCase;
use UnexpectedValueException;

final class ValidEnumerationValueTest extends TestCase
{
    /**
     * @var array|int[]
     */
    protected array $acceptableValues;

    /**
     * @var ValidEnumerationValue
     */
    protected ValidEnumerationValue $rule;

    /**
     * Configures the test cases by instantiating the validation rule.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->acceptableValues = range(1, 5);

        $this->rule = Mockery::mock(ValidEnumerationValue::class);
        $this->rule->makePartial()
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('instantiateEnumeration')
            ->with(Mockery::type('int'))
            ->andReturnUsing(function($enumerationValue) {
                if (!in_array($enumerationValue, $this->acceptableValues)) {
                    throw new UnexpectedValueException();
                }
            });
    }

    /**
     * Ensures that the validation rule will return `true` if the provided value exists in the enumeration.
     *
     * @group rules
     *
     * @return void
     */
    public function test_passes_with_valid_value()
    {
        $this->assertTrue($this->rule->passes('test', 1));
    }

    /**
     * Ensures that the validation rule will return `false` if the provided value does not exist in the enumeration.
     * The provided value of 10 does not exist in the mocked enumeration.
     *
     * @group rules
     *
     * @return void
     */
    public function test_fails_if_value_out_of_enumeration_range()
    {
        $this->assertFalse($this->rule->passes('test', 10));
    }

    /**
     * If a string of an integer value is provided, ensure it is converted to an integer.
     *
     * @group rules
     *
     * @return void
     */
    public function test_casts_string_to_integer()
    {
        $this->assertTrue($this->rule->passes('test', '1'));
    }
}
