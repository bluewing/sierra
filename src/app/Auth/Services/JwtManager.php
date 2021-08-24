<?php

namespace Bluewing\Auth\Services;

use Bluewing\Auth\Contracts\Claimable;
use Carbon\CarbonImmutable;
use Lcobucci\Clock\SystemClock;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Encoding\CannotDecodeContent;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Token;
use Lcobucci\JWT\Token\InvalidTokenStructure;
use Lcobucci\JWT\Token\RegisteredClaims;
use Lcobucci\JWT\Token\UnsupportedHeaderFound;
use Lcobucci\JWT\UnencryptedToken;
use Lcobucci\JWT\Validation\Constraint\IssuedBy;
use Lcobucci\JWT\Validation\Constraint\PermittedFor;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Validation\Constraint\SignedWith;
use Lcobucci\JWT\Validation\Constraint\StrictValidAt;

/**
 * Managers the construction and verification of JSON Web Tokens for Bluewing properties.
 */
class JwtManager {

    /**
     * @var Configuration
     */
    private Configuration $config;

    /**
     * @var string
     */
    private string $bluewingIssuer = 'Bluewing';

    /**
     * @var int
     */
    private int $validityDurationInMinutes = 15;

    /**
     * Constructor for `JwtManager`.
     *
     * @param string $permitted - What scope is this JWT permitted for?
     * @param string $key - The private key that should be used to sign the JWT.
     */
    public function __construct(private string $permitted, private string $key)
    {
        $this->config = Configuration::forSymmetricSigner(
            new Sha256(),
            InMemory::plainText($this->key)
        );

        $this->config->setValidationConstraints(
            new IssuedBy($this->bluewingIssuer),
            new PermittedFor($this->permitted),
            new StrictValidAt(SystemClock::fromUTC()),
            new SignedWith(new Sha256(), InMemory::plainText($this->key))
        );
    }

    /**
     * Builds a JWT or the entity which implements the `Claimable` contract. Usually, this is a `Member`.
     *
     * @param Claimable $claimable - The entity which is vended a JSON Web Token.
     *
     * @return string - The completed JWT, prefixed with the string 'Bearer'.
     */
    public function buildJwtFor(Claimable $claimable): string
    {
        return 'Bearer ' . $this->buildJwt($claimable)->toString();
    }

    /**
     * Constructs a `Token` object using information supplied by the `Claimable` implementor. JWTs generated will be
     * valid for fifteen minutes from time of generation.
     *
     * @param Claimable $claimable - The entity which implements the authentication functionality.
     *
     * @return Token - The JWT for the user.
     */
    private function buildJwt(Claimable $claimable): Token
    {
        $now = CarbonImmutable::now();

        return $this->buildCustomClaims($claimable)
            ->permittedFor($this->permitted)
            ->issuedBy($this->bluewingIssuer)
            ->issuedAt($now)
            ->canOnlyBeUsedAfter($now)
            ->expiresAt($now->addMinutes($this->validityDurationInMinutes))
            ->relatedTo($claimable->getAuthIdentifier())
            ->getToken($this->config->signer(), $this->config->signingKey());
    }

    /**
     * Configures custom claims for the JSON Web Token by retrieving an array of claims from the provided `Claimable`.
     * Each claim specified is added to the `Builder` object. If no claims are specified (i.e. `null` is returned
     * from `getClaimsForJwt`), then the `Builder` will be returned unchanged.
     *
     * @param Claimable $claimable - The `Claimable` to fetch custom claims from.
     *
     * @return Builder - The `Builder` used to build the JSON Web Token.
     */
    private function buildCustomClaims(Claimable $claimable): Builder
    {
        $claims     = $claimable->getClaimsForJwt();
        $builder    = $this->config->builder();

        if (!is_null($claims)) {
            foreach ($claims as $claimKey => $claimValue) {
                $builder = $builder->withClaim(strtolower("$this->bluewingIssuer:$claimKey"), $claimValue);
            }
        }

        return $builder;
    }

    /**
     * Retrieves a `Token` from the provided `jwtString`. If the string is prefixed with "Bearer", strip it from the
     * `jwtString`.
     *
     * @param string $jwtString - A string of the `Token`.
     *
     * @return Token - The parsed `Token` object.
     */
    public function jwtFromHeader(string $jwtString): Token
    {
        if ($this->doesJwtStringStartWithBearer($jwtString)) {
            $jwtString = $this->stripBearer($jwtString);
        }
        return $this->config->parser()->parse($jwtString);
    }

    /**
     * Verifies the `Token` by extracting it from its string state in the `Authorization` header, parses it, and then
     * verifies it against the `Key` provided.
     *
     * @param string $jwtStringToVerify - The string representation of the `Token`.
     *
     * @return bool - `true` if the JWT verifies successfully, `false` if the JWT is invalid or otherwise not
     * verifiable.
     */
    public function isJwtVerified(string $jwtStringToVerify): bool
    {
        if (!$this->doesJwtStringStartWithBearer($jwtStringToVerify)) {
            return false;
        }

        try {
            $token = $this->jwtFromHeader($jwtStringToVerify);
            return $this->config->validator()->validate($token, ...$this->config->validationConstraints());
        } catch (CannotDecodeContent|InvalidTokenStructure|UnsupportedHeaderFound) {
            return false;
        }
    }

    /**
     * Retrieves a particular `RegisteredClaims` claim (a claim that is listed in the IANA JSON Web Token Claims
     * registry), or a custom claim identified by a string, from the provided JWT string.
     *
     * @param string $jwtString - The JWT string to retrieve the claim from.
     * @param RegisteredClaims|string $claim - The claim that is being retrieved.
     *
     * @return mixed - The value of the claim requested, or `null` if no claim exists in the JSON Web Token.
     */
    public function getClaimFromJwt(string $jwtString, RegisteredClaims|string $claim): mixed
    {
        $token = $this->jwtFromHeader($jwtString);
        assert($token instanceof UnencryptedToken);
        return $token->claims()->get($claim);
    }

    /**
     * Ensures the token string provided is prefixed with the string "Bearer".
     *
     * @param string $tokenStringToVerify - The string to check.
     *
     * @return bool - `true` if the token does begin with "Bearer", `false` otherwise.
     */
    private function doesJwtStringStartWithBearer(string $tokenStringToVerify): bool
    {
        return substr($tokenStringToVerify, 0, 6) === "Bearer";
    }

    /**
     * Splits the provided `tokenString` into an array separated by the space in the token string, and returns the
     * token component only. The function must only be called if the token string conforms to the expected design.
     *
     * @param string $tokenString - The string to strip.
     *
     * @return string - The stripped token.
     */
    private function stripBearer(string $tokenString): string
    {
        return explode(" ", $tokenString)[1];
    }
}
