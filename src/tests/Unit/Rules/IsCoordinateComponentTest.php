<?php

namespace Tests\Unit\Rules;

use Bluewing\Rules\IsCoordinateComponent;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class IsCoordinateComponentTest extends TestCase
{
    /**
     * @var MockObject|IsCoordinateComponent
     */
    private IsCoordinateComponent|MockObject $instance;

    protected function setUp(): void
    {
        $this->instance = $this->getMockForAbstractClass(IsCoordinateComponent::class);
    }

    /**
     * @return array
     */
    public function validIntegersAndFloatsProvider(): array
    {
        return [
            [(int) -90], [(float) -90.000000],
            [(int) 0], [(float) 0.000000],
            [(int) 1], [(float) 1.234567],
            [(int) 90], [(float) 90.000000]
        ];
    }

    /**
     * @return array
     */
    public function invalidIntegersAndFloatsProvider(): array
    {
        return [
            [0x432],            // Out of range hexadecimal
            ['string'],         // String
            ['1.234567'],       // String containing an integer
            ['123.456.789'],    // String containing integer characters, but invalid
            ['-12.45-10']       // String containing integer characters, but invalid
        ];
    }

    /**
     * @return array
     */
    public function inBoundProvider(): array
    {
        return [
            [(int) 0], [(float) 0.000000],
            [(int) 1], [(float) 0.000001],
            [(int) -1], [(float) -0.000001],
            [(int) 90], [(float) 90.000000],
            [(int) -90], [(float) -90.000000]
        ];
    }

    /**
     * @return array
     */
    public function outOfBoundProvider(): array
    {
        return [
            [(float) 90.000001], [(int) 91], [(float) 123.456789],
            [(float) -90.000001], [(int) -91], [(float) -123.456789]
        ];
    }

    /**
     * @dataProvider validIntegersAndFloatsProvider
     *
     * @param int|float $testValue -
     *
     * @return void
     */
    public function test_values_that_are_integers_or_floats_pass(int|float $testValue): void
    {
        $this->assertTrue(
            $this->instance->isCoordinate($testValue, 90)
        );
    }

    /**
     * @dataProvider invalidIntegersAndFloatsProvider
     *
     * @param mixed $testValue -
     *
     * @return void
     */
    public function test_values_that_are_not_integers_or_floats_fail(mixed $testValue): void
    {
        $this->assertFalse(
            $this->instance->isCoordinate($testValue, 90)
        );
    }

    /**
     * @dataProvider inBoundProvider
     *
     * @param int|float $testValue -
     *
     * @return void
     */
    public function test_in_bound_values_pass(int|float $testValue): void
    {
        $this->assertTrue(
            $this->instance->isCoordinate($testValue, 90)
        );
    }

    /**
     * @dataProvider outOfBoundProvider
     *
     * @param int|float $testValue -
     *
     * @return void
     */
    public function test_out_of_bound_values_fail(int|float $testValue): void
    {
        $this->assertFalse(
            $this->instance->isCoordinate($testValue, 90)
        );
    }
}