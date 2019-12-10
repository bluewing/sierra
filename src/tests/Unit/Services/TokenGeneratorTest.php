<?php

namespace Tests\Unit\Services;

use Bluewing\Services\TokenGenerator;
use PHPUnit\Framework\TestCase;

class TokenGeneratorTest extends TestCase
{
    /**
     * Test that the class can be instantiated.
     *
     * @return void
     */
    public function test_can_be_instantiated(): void
    {
        $this->assertInstanceOf(
            TokenGenerator::class,
            new TokenGenerator()
        );
    }

    /**
     * Test the token is created of the specified length.
     *
     * @return void
     *
     * @throws Exception
     */
    public function test_token_is_of_specified_length(): void
    {
        $tokenGenerator = new TokenGenerator();
        $token = $tokenGenerator->generate(64);

        $this->assertEquals(64, strlen($token));
    }

    /**
     * Test that two tokens that are created are not the same.
     *
     * @return void
     *
     * @throws Exception
     */
    public function test_token_is_unique(): void
    {
        $tokenGenerator = new TokenGenerator();

        $this->assertNotEquals(
            $tokenGenerator->generate(64),
            $tokenGenerator->generate(64)
        );
    }

    /**
     * Test that the token can be prepended with a prefix if needed.
     *
     * @return void
     *
     * @throws Exception
     */
    public function test_token_can_be_prepended_with_prefix(): void
    {
        $tokenGenerator = new TokenGenerator();

        $this->assertStringStartsWith("tok_", $tokenGenerator->generate(64,  "tok"));
    }

    /**
     * Tokens should be trimmed to length if a prefix is provided. ensure the length is consistent with expectations.
     *
     * @return void
     *
     * @throws Exception
     */
    public function test_token_length_with_prefix_can_be_trimmed(): void
    {
        $tokenGenerator = new TokenGenerator();
        $tokenLength = 64;
        $tokenPrefix = "tok";

        $token = $tokenGenerator->generate($tokenLength, $tokenPrefix, true);

        $this->assertEquals(strlen($token), $tokenLength);
    }

    /**
     * If a token prefix is specified, and `trimToLength1 is false, the length of the token will be the provided token
     * length, plus the length of the prefix.
     *
     * @return void
     *
     * @throws Exception
     */
    public function test_token_length_with_prefix_is_length_of_prefix_plus_length_of_token(): void
    {
        $tokenGenerator = new TokenGenerator();
        $tokenLength = 64;
        $tokenPrefix = "tok";

        $token = $tokenGenerator->generate($tokenLength, $tokenPrefix, false);

        $this->assertEquals(strlen($token), $tokenLength + strlen($tokenPrefix) + 1);
    }
}
