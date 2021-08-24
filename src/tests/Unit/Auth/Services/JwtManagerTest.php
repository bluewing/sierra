<?php

namespace Tests\Unit\Auth;

use Carbon\CarbonImmutable;
use Exception;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Str;
use Mockery;
use PHPUnit\Framework\TestCase;
use Bluewing\Auth\Services\JwtManager;

final class JwtManagerTest extends TestCase
{
    /**
     * An instance of the `JwtManager`.
     *
     * @var JwtManager
     */
    protected JwtManager $jwtManager;

    /**
     * A mocked instance of an `Authenticatable`.
     *
     * @var Authenticatable
     */
    protected Authenticatable $authContract;

    /**
     * The user ID for the JSON Web Token, that is stored in the `sub` claim of the JWT.
     *
     * @var string
     */
    protected string $subject;

    /**
     * Sets up each test case. Instantiates an instance of JwtManager, creates a UUID, and mocks a
     * `UserOrganizationContract` for each test.
     *
     * @return void
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->jwtManager   = new JwtManager('bluewing', 'base64:'.base64_encode(random_bytes(32)));
        $this->subject      = Str::uuid()->toString();
        $this->authContract = $this->mockAuthContract($this->subject);
    }

    /**
     * Helper function that uses `Mockery` to fake an instance of `Authenticatable`.
     *
     * @param string $subject - The ID of the `Authenticatable`.
     *
     * @return Authenticatable - An instance conforming to the `Authenticatable` that has a method called
     * `getAuthIdentifier` returning the UUID.
     */
    protected function mockAuthContract(string $subject): Authenticatable
    {
        $authContract = Mockery::mock(Authenticatable::class);
        $authContract->allows()->getAuthIdentifier()->andReturns($subject);
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
        $token      = $this->jwtManager->jwtFromHeader($jwt);
        $claimType  = 'iss';

        $this->assertTrue($token->claims()->has($claimType));
        $this->assertEquals('Bluewing', $token->claims()->get($claimType));
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
        $token      = $this->jwtManager->jwtFromHeader($jwt);

        $this->assertEqualsWithDelta(time() + (60 * 15), $token->claims()->get('exp')->getTimestamp(), 1);
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
        $token      = $this->jwtManager->jwtFromHeader($jwt);
        $claimType  = 'aud';

        $this->assertTrue($token->claims()->has($claimType));
        $this->assertContains('bluewing', $token->claims()->get($claimType));
    }

    /**
     * Check that the `subject` attribute of the token is properly set.
     *
     * @group jwt
     *
     * @return void
     */
    public function test_jwt_has_subject_set_correctly(): void
    {
        $jwt        = $this->jwtManager->buildJwtFor($this->authContract);
        $token      = $this->jwtManager->jwtFromHeader($jwt);
        $claimType  = 'sub';

        $this->assertTrue($token->claims()->has($claimType));
        $this->assertEquals($this->subject, $token->claims()->get($claimType));
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
     * Test that the JWT is not verified if the properties of the token are tampered with. Furthermore, aJWT that has
     * invalid content (for example, being base64 with the `=` padding remaining on the end of the base64 string)
     * should not be able to be verified.
     *
     * @group jwt
     *
     * @return void
     */
    public function test_tampered_jwt_cannot_be_verified(): void
    {
        $scenarios = [
            'tamperedJwt' => function($jsonPayload) {
                $jsonPayload->sub = Str::uuid()->toString();
                // Re-encode. Note that base64 padding ins trimmed as per RFC7515
                // https://datatracker.ietf.org/doc/html/rfc7515#section-2
                $jwtComponents[1] = trim(base64_encode(json_encode($jsonPayload)), "=");
                return implode(".", $jwtComponents);
            },

            'tamperedJwtWithInvalidContent' => function($jsonPayload) {
                $jsonPayload->sub = Str::uuid()->toString();
                $jwtComponents[1] = base64_encode(json_encode($jsonPayload));
                return implode(".", $jwtComponents);
            }
        ];

        // Create the token.
        $jwt = $this->jwtManager->buildJwtFor($this->authContract);

        // Split the token into its components
        $jwtComponents = explode(".", $jwt);

        // Decode the base64 formatting, convert to JSON, and update a parameter.
        $jsonPayload = json_decode(base64_decode($jwtComponents[1]));

        // Fetch each invalid JWT.
        foreach ($scenarios as $scenario) {
            $jwt = $scenario($jsonPayload);
            // Assert that the token is not verified.
            $this->assertFalse($this->jwtManager->isJwtVerified($jwt));
        }
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
        $thirtyMinutesAgo = CarbonImmutable::now()->subMinutes(30);
        CarbonImmutable::setTestNow($thirtyMinutesAgo);

        $jwt = $this->jwtManager->buildJwtFor($this->authContract);

        CarbonImmutable::setTestNow(); // clear the mock.
        $this->assertFalse($this->jwtManager->isJwtVerified($jwt));
    }
}
