<?php

namespace Bluewing\SharedServer\Jwt;

use Bluewing\SharedServer\Contracts\BluewingAuthenticationContract;
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
     * What scope is this token permitted for?
     */
    private $permitted;

    /**
     * The private key that should be used to sign the token.
     */
    private $key;

    /**
     * Constructor for JwtManager.
     *
     * @param string $permitted - What scope is this token permitted for?
     *
     * @param string $key - The private key that should be used to sign the token.
     */
    public function __construct(string $permitted, string $key) {
        $this->permitted = $permitted;
        $this->key = $key;
    }

    /**
     * Builds a token for the entity which implements `BluewingAuthenticationContract`. Usually,
     * this is a `UserOrganization`.
     *
     * @param BluewingAuthenticationContract $authenticatable - The entity which implements the
     * authentication functionality.
     *
     * @return string - The completed token, prefixed with the string 'Bearer'.
     */
    public function buildTokenFor(BluewingAuthenticationContract $authenticatable): string {
        return 'Bearer ' . $this->buildToken($authenticatable);
    }

    /**
     * Constructs a `Token` object using information supplied by the `BluewingAuthenticationContract`
     * implementor.
     *
     * @param BluewingAuthenticationContract $authenticatable - The entity which implements the
     * authentication functionality.
     *
     * @return Token
     */
    private function buildToken(BluewingAuthenticationContract $authenticatable): Token {
        return (new Builder())->issuedBy('Bluewing LLC')
            ->permittedFor($this->permitted)
            ->issuedAt(time())
            ->expiresAt(time() + 3600)
            ->withClaim('uid', $authenticatable->getAuthIdentifier())
            ->getToken(new Sha256(), new Key($this->key));
    }

    /**
     * Retrieves a `Token` from the provided `tokenString`. If the string is prefixed with "Bearer",
     * strip it from the token string.
     *
     * @param string $tokenString - A string of the `Token`.
     *
     * @return Token - The parsed `Token` object.
     */
    public function tokenFromString(string $tokenString): Token {
        if ($this->doesTokenStringStartWithBearer($tokenString)) {
            $tokenString = $this->stripBearer($tokenString);
        }
        return (new Parser())->parse($tokenString);
    }

    /**
     * Verifies the `Token` by extracting it from its string state in the `Authorization` header, parses it, and then
     * verifies it against the `Key` provided.
     *
     * @param string $tokenStringToVerify - The string representation of the `Token`.
     *
     * @return bool - `true` if the token verifies successfully, `false` if the token is invalid or otherwise
     * not verifiable.
     */
    public function isTokenVerified(string $tokenStringToVerify): bool {
        if (!$this->doesTokenStringStartWithBearer($tokenStringToVerify)) {
            return false;
        }

        $tokenString = $this->stripBearer($tokenStringToVerify);
        $token = $this->tokenFromString($tokenString);

        return $this->isTokenValid($token) && $token->verify(new Sha256(), new Key($this->key));
    }

    /**
     * Ensures the provided `Token` is valid by comparing it against the `ValidationData`.
     *
     * @param Token $token - The token to check for validity.
     *
     * @return bool - `true` if the token is valid, `false` if it is not.
     */
    private function isTokenValid(Token $token): bool {
        $data = new ValidationData();
        $data->setIssuer('Bluewing LLC');
        $data->setAudience($this->permitted);

        return $token->validate($data);
    }

    /**
     * Ensures the token string provided is prefixed with the string "Bearer".
     * 
     * @param string $tokenStringToVerify - The string to check.
     * 
     * @return bool - `true` if the token does begin with "Bearer", `false` otherwise.
     */
    private function doesTokenStringStartWithBearer(string $tokenStringToVerify): bool {
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
    private function stripBearer(string $tokenString): string {
        return explode(" ", $tokenString)[1];
    }
}
