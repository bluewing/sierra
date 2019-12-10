<?php

namespace Tests\Unit\Auth;

use Bluewing\Contracts\UserOrganizationContract;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Mockery;
use PHPUnit\Framework\TestCase;
use Bluewing\Auth\JwtManager;

final class JwtManagerTest extends TestCase
{
    /**
     * An instance of the `JwtManager`.
     *
     * @var JwtManager
     */
    protected JwtManager $jwtManager;

    /**
     * A mocked instance of a `UserOrganizationContract`.
     *
     * @var UserOrganizationContract
     */
    protected UserOrganizationContract $authContract;

    /**
     * The user ID for the JSON Web Token.
     *
     * @var string
     */
    protected string $uid;

    /**
     * Sets up each test case. Instantiates an instance of JwtManager, creates a UUID, and mocks a
     * `UserOrganizationContract` for each test.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->jwtManager = new JwtManager('bluewing', 'key');
        $this->uid = Str::uuid()->toString();
        $this->authContract = $this->mockAuthContract($this->uid);
    }

    /**
     * Helper function that uses `Mockery` to fake an instance of `UserOrganizationContract`.
     *
     * @param string $uid - The ID of the `UserOrganizationContract`.
     *
     * @return UserOrganizationContract - An instance conforming to the `UserOrganizationContract` that has a
     * method called `getAuthIdentifier` returning the UUID.
     */
    protected function mockAuthContract(string $uid): UserOrganizationContract
    {
        $authContract = Mockery::mock(UserOrganizationContract::class);
        $authContract->allows()->getAuthIdentifier()->andReturns($uid);
        return $authContract;
    }

    /**
     * Test that the class can be created.
     *
     * @group jwt
     *
     * @return void
     */
    public function test_can_be_created_with_valid_key_and_permission(): void
    {
        $this->assertInstanceOf(
            JwtManager::class,
            $this->jwtManager
        );
    }

    /**
     * Test that the JWT created begins with the prefix "Bearer".
     *
     * @group jwt
     *
     * @return void
     */
    public function test_jwt_begins_with_bearer(): void
    {
        $jwt = $this->jwtManager->buildJwtFor($this->authContract);
        $this->assertStringStartsWith("Bearer", $jwt);
    }

    /**
     * Test the JWT is correctly issued by "Bluewing".
     *
     * @group jwt
     *
     * @return void
     */
    public function test_jwt_contains_correct_issuer(): void
    {
        $jwt        = $this->jwtManager->buildJwtFor($this->authContract);
        $token      = $this->jwtManager->jwtFromString($jwt);

        $this->assertTrue($token->hasClaim('iss'));
        $this->assertEquals('Bluewing', $token->getClaim('iss'));
    }

    /**
     * JWTs, when issued, are valid for 15 minutes from the current time only.
     *
     * @group jwt
     *
     * @return void
     */
    public function test_jwt_is_valid_for_15_minutes(): void
    {
        $jwt        = $this->jwtManager->buildJwtFor($this->authContract);
        $token      = $this->jwtManager->jwtFromString($jwt);

        $this->assertEqualsWithDelta(time() + (60 * 15), $token->getClaim('exp'), 1);
    }

    /**
     * Check the token audience is correctly set to the permitted string that was passed in.
     *
     * @group jwt
     *
     * @return void
     */
    public function test_jwt_is_permitted_properly(): void
    {
        $jwt        = $this->jwtManager->buildJwtFor($this->authContract);
        $token      = $this->jwtManager->jwtFromString($jwt);

        $this->assertTrue($token->hasClaim('aud'));
        $this->assertEquals('bluewing', $token->getClaim('aud'));
    }

    /**
     * Check that the `uid` attribute of the token is properly set.
     *
     * @group jwt
     *
     * @return void
     */
    public function test_jwt_has_uid_set_correctly(): void
    {
        $jwt        = $this->jwtManager->buildJwtFor($this->authContract);
        $token      = $this->jwtManager->jwtFromString($jwt);

        $this->assertTrue($token->hasClaim('uid'));
        $this->assertEquals($this->uid, $token->getClaim('uid'));
    }

    /**
     * Ensure that the JWT actually verifies properly if created.
     *
     * @group jwt
     *
     * @return void
     */
    public function test_jwt_verifies(): void
    {
        $jwt = $this->jwtManager->buildJwtFor($this->authContract);
        $this->assertTrue($this->jwtManager->isJwtVerified($jwt));
    }

    /**
     * Test that the JWT is not verified if the properties of the token are tampered with.
     *
     * @group jwt
     *
     * @return void
     */
    public function test_tampered_jwt_cannot_be_verified(): void
    {
        // Create the token.
        $jwt = $this->jwtManager->buildJwtFor($this->authContract);

        // Split the token into its components
        $jwtComponents = explode(".", $jwt);

        // Decode the base64 formatting, convert to JSON, and update a parameter.
        $jsonPayload = json_decode(base64_decode($jwtComponents[1]));
        $jsonPayload->uid = 2;

        // Re-encode
        $jwtComponents[1] = base64_encode(json_encode($jsonPayload));
        $jwt = implode(".", $jwtComponents);

        // Assert that the token is not verified.
        $this->assertFalse($this->jwtManager->isJwtVerified($jwt));
    }

    /**
     * If the token expires, ensure that the JWT does not verify. To test this, we freeze time by implementing
     * `Carbon::setTestNow` to ensure `Carbon::now()` returns a time thirty minutes ago. This works because internally,
     * JwtManager utilizes carbon to set the valid datetime for the JSON Web Token.
     *
     * @see https://www.integer-net.com/testing-date-time-with-clock-objects/
     *
     * @group jwt
     * @group timeSensitive
     *
     * @return void
     */
    public function test_expired_jwt_cannot_be_verified(): void
    {
        $thirtyMinutesAgo = Carbon::now()->subMinutes(30);
        Carbon::setTestNow($thirtyMinutesAgo);

        $jwt = $this->jwtManager->buildJwtFor($this->authContract);

        Carbon::setTestNow(); // clear the mock.
        $this->assertFalse($this->jwtManager->isJwtVerified($jwt));
    }
}
