<?php

namespace Bluewing\Auth\Services;

use Bluewing\Contracts\MemberContract;
use Carbon\CarbonImmutable;
use Lcobucci\Clock\SystemClock;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Token;
use Lcobucci\JWT\Validation\Constraint\IssuedBy;
use Lcobucci\JWT\Validation\Constraint\PermittedFor;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Validation\Constraint\SignedWith;
use Lcobucci\JWT\Validation\Constraint\ValidAt;

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
     * Constructor for JwtManager.
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
            new ValidAt(SystemClock::fromUTC()),
            new SignedWith(new Sha256(), InMemory::plainText($this->key))
        );
    }

    /**
     * Builds a JWT or the entity which implements `MemberContract`. Usually, this is a `Member`.
     *
     * @param MemberContract $authenticatable - The entity which implements the
     * authentication functionality.
     *
     * @return string - The completed JWT, prefixed with the string 'Bearer'.
     */
    public function buildJwtFor(MemberContract $authenticatable): string
    {
        return 'Bearer ' . $this->buildJwt($authenticatable)->toString();
    }

    /**
     * Constructs a `Token` object using information supplied by the `MemberContract`
     * implementor. JWTs generated will be valid for fifteen minutes from time of generation.
     *
     * @param MemberContract $authenticatable - The entity which implements the
     * authentication functionality.
     *
     * @return Token - The JWT for the user.
     */
    private function buildJwt(MemberContract $authenticatable): Token
    {
        $now = CarbonImmutable::now();

        return $this->config->builder()
            ->permittedFor($this->permitted)
            ->issuedBy($this->bluewingIssuer)
            ->issuedAt($now)
            ->expiresAt($now->addMinutes($this->validityDurationInMinutes))
            ->relatedTo($authenticatable->getAuthIdentifier())
            ->getToken($this->config->signer(), $this->config->signingKey());
    }

    /**
     * Retrieves a `Token` from the provided `jwtString`. If the string is prefixed with "Bearer",
     * strip it from the `jwtString`.
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
     * @return bool - `true` if the JWT verifies successfully, `false` if the JWT is invalid or otherwise
     * not verifiable.
     */
    public function isJwtVerified(string $jwtStringToVerify): bool
    {
        if (!$this->doesJwtStringStartWithBearer($jwtStringToVerify)) {
            return false;
        }

        return $this->config->validator()->validate(
            $this->jwtFromHeader($jwtStringToVerify),
            ...$this->config->validationConstraints()
        );
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
     * Splits the provided `tokenString` into an array separated by the space in the token
     * string, and returns the token component only.
     *
     * The function must only be called if the token string conforms to the expected
     * design.
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
