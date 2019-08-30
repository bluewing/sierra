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
 * Managers the construction and verification of
 */
class JwtManager {

    /**
     * @param BluewingAuthenticationContract $authenticatable
     *
     * @return string
     */
    public function buildTokenFor(BluewingAuthenticationContract $authenticatable): string {
        return 'Bearer ' . $this->buildToken($authenticatable);
    }

    /**
     * Constructs a `Token` object
     *
     * @param BluewingAuthenticationContract $authenticatable
     *
     * @return Token
     */
    private function buildToken(BluewingAuthenticationContract $authenticatable): Token {
        $signer = new Sha256();

        return (new Builder())->issuedBy('horizon.app')
            ->permittedFor('horizon.app')
            ->issuedAt(time())
            ->expiresAt(time() + 3600)
            ->withClaim('uid', $authenticatable->getAuthIdentifier())
            ->getToken($signer, new Key('testing'));
    }

    /**
     * @param string $tokenString
     *
     * @return bool
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

        return $token->verify(new Sha256(), new Key('testing'));
    }

    /**
     * @param Token $token
     *
     * @return bool
     */
    private function isTokenValid(Token $token): bool {
        $data = new ValidationData();
        $data->setIssuer('horizon.app');
        $data->setAudience('horizon.app');

        return $token->validate($data);
    }
}
