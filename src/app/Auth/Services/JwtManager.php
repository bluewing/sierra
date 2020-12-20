<?php

namespace Bluewing\Auth\Services;

use Bluewing\Contracts\MemberContract;
use Carbon\CarbonImmutable;
use Illuminate\Support\Carbon;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Token;
use Lcobucci\JWT\ValidationData;

/**
 * Managers the construction and verification of JSON Web Tokens for Bluewing properties.
 */
class JwtManager {

    /**
     * What scope is this JWT permitted for?
     */
    private string $permitted;

    /**
     * The private key that should be used to sign the JWT.
     *
     * @var string
     */
    private string $key;

    /**
     * Constructor for JwtManager.
     *
     * @param string $permitted - What scope is this JWT permitted for.
     * @param string $key - The private key that should be used to sign the JWT.
     */
    public function __construct(string $permitted, string $key)
    {
        $this->permitted = $permitted;
        $this->key = $key;
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
        return 'Bearer ' . $this->buildJwt($authenticatable);
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

        return (new Builder())->issuedBy('Bluewing')
            ->permittedFor($this->permitted)
            ->issuedAt($now)
            ->expiresAt($now->addMinutes(15))
            ->withClaim('mid', $authenticatable->getAuthIdentifier())
            ->getToken(new Sha256(), new Key($this->key));
    }

    /**
     * Retrieves a `Token` from the provided `jwtString`. If the string is prefixed with "Bearer",
     * strip it from the `jwtString`.
     *
     * @param string $jwtString - A string of the `Token`.
     *
     * @return Token - The parsed `Token` object.
     */
    public function jwtFromString(string $jwtString): Token
    {
        if ($this->doesJwtStringStartWithBearer($jwtString)) {
            $jwtString = $this->stripBearer($jwtString);
        }
        return (new Parser())->parse($jwtString);
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

        $jwt = $this->jwtFromString($jwtStringToVerify);

        return $this->isJwtValid($jwt) && $jwt->verify(new Sha256(), new Key($this->key));
    }

    /**
     * Ensures the provided `Token` is valid by comparing it against the `ValidationData`.
     *
     * @param Token $jwt - The JWT to check for validity.
     *
     * @return bool - `true` if the JWT is valid, `false` if it is not.
     */
    private function isJwtValid(Token $jwt): bool
    {
        $data = new ValidationData();
        $data->setIssuer('Bluewing');
        $data->setAudience($this->permitted);

        return $jwt->validate($data);
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
