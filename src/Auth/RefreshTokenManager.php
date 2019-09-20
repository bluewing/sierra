<?php

namespace Bluewing\Auth;

use Bluewing\Models\RefreshToken;

class RefreshTokenManager {

    /**
     * @param BluewingAuthenticationContract $authenticatable -
     *
     * @return string - The `RefreshToken` string that can be exchanged for a new JSON web token later.
     */
    public function buildRefreshTokenFor(BluewingAuthenticationContract $authenticatable): string {
        $refreshToken = RefreshToken::create([
            'token'     => null,
            'device'    => null
        ]);

        return $refreshToken->token;
    }

    /**
     *
     */
    public function findRefreshTokenOrFail(): RefreshToken {

    }

    /**
     * Given a refresh token string, revokes the `RefreshToken` entry in the database.
     *
     * @param $refreshTokenString -
     *
     * @return void
     */
    public function revokeRefreshToken($refreshTokenString): void {
        RefreshToken::where('token', $refreshTokenString)->delete();
    }
}