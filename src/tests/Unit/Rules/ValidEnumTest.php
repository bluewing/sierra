<?php

namespace Tests\Unit\Rules;

use Bluewing\Rules\ValidEnum;
use Mockery;
use PHPUnit\Framework\TestCase;

final class ValidEnumTest extends TestCase
{
    /**
     * The array of acceptable enumeration values that are used to shim an enumeration's values.
     *
     * @var array
     */
    protected array $acceptableValues;

    /**
     * The instance of the `ValidEnum` rule.
     *
     * @var ValidEnum
     */
    protected ValidEnum $rule;

    /**
     * Configures the test cases by instantiating the validation rule.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->acceptableValues = [1, 2, 3, 'valid_string'];

        $this->rule = Mockery::mock(ValidEnum::class);
        $this->rule->makePartial()
            ->shouldReceive('passes')
            ->andReturnUsing(function($attribute, $enumValue) {
                return in_array($enumValue, $this->acceptableValues);
            });
    }

    /**
     * PHPUnit data provider for the valid enumeration values of particular types that are considered valid (integers
     * and strings).
     *
     * @return array The `array` of enumeration values to provide to the data provider.
     */
    public function validEnumerationTypeProvider(): array
    {
        return [
            [1],
            ['valid_string']
        ];
    }

    /**
     * PHPUnit data provider for the invalid enumeration values of particular types that are not valid (anything that
     * isn't an integer or string).
     *
     * @return array The `array` of enumeration values to provide to the data provider.
     */
    public function invalidEnumerationTypeProvider(): array
    {
        return [
            [null],
            [false],
            [[1]]
        ];
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
        $this->assertTrue($this->rule->passes('attribute', 1));
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
        $this->assertFalse($this->rule->passes('attribute', 10));
    }

    /**
     * If a valid value is provided to the `ValidEnum` rule, then it should be considered as passing if it is also a
     * valid value for the enumeration.
     *
     * @dataProvider validEnumerationTypeProvider
     * @group rules
     *
     * @param mixed $validEnumerationType
     *
     * @return void
     */
    public function test_supports_both_integers_and_strings(mixed $validEnumerationType)
    {
        $this->assertTrue($this->rule->passes('attribute', $validEnumerationType));
    }

    /**
     * If an invalid value is provided to the `ValidEnum` rule, such as `null`, `false`, or an `array`, all of which
     * cannot ever be valid backed enumeration values, then `false` should be returned.
     *
     * @dataProvider invalidEnumerationTypeProvider
     * @group rules
     *
     * @param mixed $invalidEnumerationType The valid enumeration type that is being tested.
     *
     * @return void
     */
    public function test_errors_if_provided_non_string_or_integer_values(mixed $invalidEnumerationType)
    {
        $this->assertFalse($this->rule->passes('attribute', $invalidEnumerationType));
    }
}
