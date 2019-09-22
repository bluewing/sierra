<?php

use Bluewing\Services\TokenGenerator;
use PHPUnit\Framework\TestCase;
use Bluewing\Auth\RefreshTokenManager;

class RefreshTokenManagerTest extends TestCase
{
    /**
     * Test that the class can be instantiated.
     *
     * @return void
     */
    public function test_can_be_created() {
        $tokenGenerator = new TokenGenerator();

        $this->assertInstanceOf(
            RefreshTokenManager::class,
            new RefreshTokenManager($tokenGenerator)
        );
    }

    /**
     * Test that the `RefreshTokenManager` will create a token with the correct properties if
     * instructed.
     *
     * @return void
     */
    public function test_creates_refresh_token() {

    }

    /**
     * Test that the `RefreshTokenManager` will find the `RefreshToken` by the provided string.
     *
     * @return void
     */
    public function test_finds_refresh_token_by_string() {

    }

    /**
     * Test that the `RefreshTokenManager` will throw an `Exception` if the token cannot be found by the
     * string provided.
     *
     * @return void
     */
    public function test_fails_if_refresh_token_cannot_be_found() {

    }

    /**
     * Test that the `RefreshTokenManager` successfully revokes an existing token by a string value.
     *
     * @return void
     */
    public function test_revokes_refresh_token() {

    }
}
