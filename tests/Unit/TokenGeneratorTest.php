<?php


use Bluewing\Services\TokenGenerator;
use PHPUnit\Framework\TestCase;

class TokenGeneratorTest extends TestCase
{
    /**
     * Test that the class can be instantiated.
     *
     * @return void
     */
    public function test_can_be_instantiated() {
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
    public function test_token_is_of_specified_length() {
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
    public function test_token_is_unique() {
        $tokenGenerator = new TokenGenerator();

        $this->assertNotEquals(
            $tokenGenerator->generate(64),
            $tokenGenerator->generate(64)
        );
    }
}
