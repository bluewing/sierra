<?php


namespace Tests\Unit\Rules;


use Bluewing\Rules\Matches;
use PHPUnit\Framework\TestCase;

final class MatchesTest extends TestCase
{
    /**
     * A single string should match a haystack of available options.
     *
     * @group rules
     *
     * @return void
     */
    public function test_string_matches_successfully()
    {
        $testRuns = [
            'single'    => ['single'],
            'double'    => ['single', 'double'],
            'multiple'  => ['multiple', 'multiple', 'double']
        ];

        foreach ($testRuns as $needle => $haystack) {
            $rule = new Matches($haystack);
            $this->assertTrue($rule->passes('test', $needle));
        }
    }

    /**
     * The rule should always return true if an integer provided is in the array, even if the integer is expressed
     * differently in literal form (i.e. octal to hexadecimal).
     *
     * @group rules
     *
     * @return void
     */
    public function test_integer_matches_successfully()
    {
        $testRuns = [
            12          => [12],
            022         => [18, 15],
            0x1c        => [14, 28],
            0b100011    => [35]
        ];

        foreach ($testRuns as $needle => $haystack) {
            $rule = new Matches($haystack);
            $this->assertTrue($rule->passes('test', $needle));
        }
    }

    /**
     * The `Matches` rule should always return false if the array of possible values is empty.
     *
     * @group rules
     *
     * @return void
     */
    public function test_nothing_matches_empty_array()
    {
        $testRuns = [null, 'string', 0x6];

        foreach ($testRuns as $needle) {
            $rule = new Matches([]);
            $this->assertFalse($rule->passes('test', $needle));
        }
    }

    /**
     * The `Matches` rule should allow a custom message to be provided if the validation fails, as the second
     * parameter to the constructor.
     *
     * @group rules
     *
     * @return void
     */
    public function test_match_supports_custom_error_message()
    {
        $rule = new Matches([], 'Custom error message');
        $this->assertEquals('Custom error message', $rule->message());
    }
}
