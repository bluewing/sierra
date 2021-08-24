<?php

namespace Tests\Unit\Auth;

use Bluewing\Auth\Contracts\Claimable;
use Carbon\CarbonImmutable;
use Exception;
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
     * A mocked instance of an `Claimable`.
     *
     * @var Claimable
     */
    protected Claimable $claimableContract;

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

        $this->jwtManager           = new JwtManager('bluewing', 'base64:'.base64_encode(random_bytes(32)));
        $this->subject              = Str::uuid()->toString();
        $this->claimableContract    = $this->mockClaimableContract($this->subject, null);
    }

    /**
     * Helper function that uses `Mockery` to fake an instance of `Claimable`.
     *
     * @param string $subject - The ID of the `Claimable`.
     * @param array|null $claims - An `array` of claims to lodge for the JWT, or `null` if none are present.
     *
     * @return Claimable - An instance conforming to the `Claimable` interface that has a method called
     * `getAuthIdentifier` returning the UUID.
     */
    protected function mockClaimableContract(string $subject, ?array $claims): Claimable
    {
        $claimsContract = Mockery::mock(Claimable::class);
        $claimsContract->allows()->getAuthIdentifier()->andReturns($subject);
        $claimsContract->allows()->getClaimsForJwt()->andReturns($claims);

        return $claimsContract;
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
        $jwt = $this->jwtManager->buildJwtFor($this->claimableContract);
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
        $jwt        = $this->jwtManager->buildJwtFor($this->claimableContract);
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
        $jwt        = $this->jwtManager->buildJwtFor($this->claimableContract);
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
        $jwt        = $this->jwtManager->buildJwtFor($this->claimableContract);
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
        $jwt        = $this->jwtManager->buildJwtFor($this->claimableContract);
        $token      = $this->jwtManager->jwtFromHeader($jwt);
        $claimType  = 'sub';

        $this->assertTrue($token->claims()->has($claimType));
        $this->assertEquals($this->subject, $token->claims()->get($claimType));
    }

    /**
     * If the `Claimable` contract object has custom claims that can be provided, they should be added to the
     * @group jwt
     *
     * @return void
     */
    public function test_custom_claims_can_be_provided_in_jwt(): void
    {
        $now = CarbonImmutable::now();
        $scenarios = [
            'claimableHasNoClaims'      => [
                'claimable'     => $this->mockClaimableContract($this->subject, null),
                'claimToGet'    => []
            ],
            'claimableHasBluewingClaim' => [
                'claimable'     => $this->mockClaimableContract($this->subject, ['trialExpiresAt' => $now]),
                'claimToGet'    => ['bluewing:trialexpiresat' => $now->toISOString()]
            ]
        ];

        foreach ($scenarios as $scenario) {
            $jwt = $this->jwtManager->buildJwtFor($scenario['claimable']);

            foreach ($scenario['claimToGet'] as $claimKeyToGet => $claimValueToGet) {
                $actualClaimValue = $this->jwtManager->getClaimFromJwt($jwt, $claimKeyToGet);
                $this->assertNotNull($actualClaimValue);
                $this->assertEquals($claimValueToGet, $actualClaimValue);
            }
        }
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
        $jwt = $this->jwtManager->buildJwtFor($this->claimableContract);
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
        $jwt = $this->jwtManager->buildJwtFor($this->claimableContract);

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

        $jwt = $this->jwtManager->buildJwtFor($this->claimableContract);

        CarbonImmutable::setTestNow(); // clear the mock.
        $this->assertFalse($this->jwtManager->isJwtVerified($jwt));
    }
}
