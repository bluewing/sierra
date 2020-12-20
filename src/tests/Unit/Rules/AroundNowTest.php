<?php


namespace Tests\Unit\Rules;


use Bluewing\Rules\AroundNow;
use Carbon\Carbon;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class AroundNowTest extends TestCase
{
    /**
     * Ensure that all `Carbon` instances are appropriately faked with the current time.
     */
    protected function setUp(): void
    {
        Carbon::setTestNow(Carbon::now());
    }

    /**
     * Ensure that all `Carbon` instances are cleared following test completion.
     */
    protected function tearDown(): void
    {
        Carbon::setTestNow();
    }

    /**
     * Any time, including this moment, that is within the allowable delta for the `AroundNow` rule will pass
     * successfully.
     *
     * @group rules
     *
     * @return void
     */
    public function test_date_time_within_delta_passes()
    {
        $scenarios = [
            Carbon::now()->toIso8601String(),
            Carbon::now(),
            Carbon::now()->subSeconds(5)->toIso8601String(),
            Carbon::now()->addSeconds(4)
        ];

        foreach ($scenarios as $scenario) {
            $this->assertTrue((new AroundNow(10))->passes('test', $scenario));
        }
    }

    /**
     * An optional `forwardDelta` parameter can be provided as the second argument to the constructor to customize
     * the forward-looking window of acceptability.
     *
     * @group rules
     *
     * @return void
     */
    public function test_date_time_within_forward_delta_passes()
    {
        $scenarios = [
            Carbon::now()->addSeconds(3),
            Carbon::now()->addSeconds(3)->toIso8601String()
        ];

        foreach ($scenarios as $scenario) {
            $this->assertTrue((new AroundNow(2,4))->passes('test', $scenario));
        }
    }

    /**
     * If the rule specifies that the allowed delta around right now is some number of seconds, even values equalling
     * that delta will be allowed, i.e. if the value provided is at the boundary of the delta.
     *
     * @group rules
     *
     * @return void
     */
    public function test_date_time_passes_if_at_bounds()
    {
        $scenarios = [
            Carbon::now()->addSeconds(5),
            Carbon::now()->addSeconds(5)->toIso8601String()
        ];

        foreach ($scenarios as $scenario) {
            $this->assertTrue((new AroundNow(5))->passes('test', $scenario));
        }
    }

    /**
     * If the provided value falls outside the bounds of what is acceptable as a definition of "now", the rule should
     * fail.
     *
     * @group rules
     *
     * @return void
     */
    public function test_date_time_fails_if_outside_bounds()
    {
        $scenarios = [
            Carbon::now()->subSeconds(23)->toIso8601String(),
            Carbon::now()->subSeconds(23),
            Carbon::now()->addSeconds(7)->toIso8601String(),
            Carbon::now()->addSeconds(7)
        ];

        foreach ($scenarios as $scenario) {
            $rule = new AroundNow(5);
            $this->assertFalse($rule->passes('test', $scenario));
        }
    }

    /**
     * The `AroundNow` rule will throw an exception if the provided delta parameters are less than zero.
     *
     * @group rules
     *
     * @return void
     */
    public function test_throws_exception_if_negative_parameter_value_provided()
    {
        $this->expectException(InvalidArgumentException::class);
        new AroundNow(-1);
    }
}
