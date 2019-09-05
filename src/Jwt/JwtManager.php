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
            ->getToken(new Sha256(), new Key($this-key));
    }

    /**
     * Verifies the `Token` by extracting it from its string state in the `Authorization` header, parses it, and then
     * verifies it against the `Key` provided.
     *
     * @param string $tokenString - The string representation of the `Token`.
     *
     * @return bool - `true` if the token verifies successfully, `false` if the token is invalid or otherwise
     * not verifiable.
     */
    public function isTokenVerified(string $tokenString): bool {
        if (substr($tokenString, 0, 6) !== "Bearer") {
            return false;
        }

        $tokenString = explode(" ", $tokenString)[1];
        $token = (new Parser())->parse($tokenString);

        if (!$this->isTokenValid($token)) {
            return false;
        }

        return $token->verify(new Sha256(), new Key($this->key));
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
}
