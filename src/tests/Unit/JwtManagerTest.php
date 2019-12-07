<?php

use Bluewing\Contracts\UserOrganizationContract;
use PHPUnit\Framework\TestCase;
use Bluewing\Auth\JwtManager;
use Bluewing\Contracts\AuthenticationContract;

final class JwtManagerTest extends TestCase
{

    /**
     * Helper function that uses `Mockery` to fake an instance of `AuthenticationContract`.
     *
     * @return UserOrganizationContract - An instance conforming to the `AuthenticationContract` that has a
     * method called `getAuthIdentifier` returning 1.
     */
    protected function mockAuthContract(): UserOrganizationContract
    {
        $authContract = Mockery::mock(UserOrganizationContract::class);
        $authContract->allows()->getAuthIdentifier()->andReturns(1);
        return $authContract;
    }

    /**
     * Helper function to create an instance of the `JwtManager` class.
     *
     * @return JwtManager - An instance of `JwtManager`.
     */
    protected function createJwtManager(): JwtManager
    {
        return new JwtManager('bluewing', 'key');
    }

    /**
     * Test that the class can be created.
     *
     * @return void
     */
    public function test_can_be_created_with_valid_key_and_permission(): void
    {
        $this->assertInstanceOf(
            JwtManager::class,
            $this->createJwtManager()
        );
    }

    /**
     * Test that the JWT created begins with the prefix "Bearer".
     *
     * @return void
     */
    public function test_jwt_begins_with_bearer(): void
    {
        $jwt = $this->createJwtManager()->buildJwtFor($this->mockAuthContract());
        $this->assertStringStartsWith("Bearer", $jwt);
    }

    /**
     * Test the JWT is correctly issued by "Bluewing".
     *
     * @return void
     */
    public function test_jwt_contains_correct_issuer(): void
    {
        $jwtManager = $this->createJwtManager();
        $jwt        = $jwtManager->buildJwtFor($this->mockAuthContract());
        $token      = $jwtManager->jwtFromString($jwt);

        $this->assertTrue($token->hasClaim('iss'));
        $this->assertEquals('Bluewing', $token->getClaim('iss'));
    }

    /**
     * JWTs, when issued, are valid for 15 minutes from the current time only.
     *
     * @return void
     */
    public function test_jwt_is_valid_for_15_minutes(): void
    {
        $jwtManager = $this->createJwtManager();
        $jwt        = $jwtManager->buildJwtFor($this->mockAuthContract());
        $token      = $jwtManager->jwtFromString($jwt);

        $this->assertEqualsWithDelta(time() + (60 * 15), $token->getClaim('exp'), 1);
    }

    /**
     * Check the token audience is correctly set to the permitted string that was passed in.
     *
     * @return void
     */
    public function test_jwt_is_permitted_properly(): void
    {
        $jwtManager = $this->createJwtManager();
        $jwt        = $jwtManager->buildJwtFor($this->mockAuthContract());
        $token      = $jwtManager->jwtFromString($jwt);

        $this->assertTrue($token->hasClaim('aud'));
        $this->assertEquals('bluewing', $token->getClaim('aud'));
    }

    /**
     * Check that the `uid` attribute of the token is properly set.
     *
     * @return void
     */
    public function test_jwt_has_uid_set_correctly(): void
    {
        $jwtManager = $this->createJwtManager();
        $jwt        = $jwtManager->buildJwtFor($this->mockAuthContract());
        $token      = $jwtManager->jwtFromString($jwt);

        $this->assertTrue($token->hasClaim('aud'));
        $this->assertEquals('bluewing', $token->getClaim('aud'));
    }

    /**
     * Ensure that the JWT actually verifies properly if created.
     *
     * @return void
     */
    public function test_jwt_verifies(): void
    {
        $jwtManager = $this->createJwtManager();
        $jwt        = $jwtManager->buildJwtFor($this->mockAuthContract());

        $this->assertTrue($jwtManager->isJwtVerified($jwt));
    }

    /**
     * Test that the JWT is not verified if the properties of the token are tampered with.
     *
     * @return void
     */
    public function test_tampered_jwt_cannot_be_verified(): void
    {
        // Create the token.
        $jwtManager = $this->createJwtManager();
        $jwt        = $jwtManager->buildJwtFor($this->mockAuthContract());

        // Split the token into its components
        $jwtComponents = explode(".", $jwt);

        // Decode the base64 formatting, convert to JSON, and update a parameter.
        $jsonPayload = json_decode(base64_decode($jwtComponents[1]));
        $jsonPayload->uid = 2;

        // Re-encode
        $jwtComponents[1] = base64_encode(json_encode($jsonPayload));
        $jwt = implode(".", $jwtComponents);

        // Assert that the token is not verified.
        $this->assertFalse($jwtManager->isJwtVerified($jwt));
    }

    /**
     * If the token expires, ensure that the JWT does not verify.
     *
     * @return void
     */
    public function test_expired_jwt_cannot_be_verified(): void
    {
        // TODO: Expire token somehow here?
    }
}
