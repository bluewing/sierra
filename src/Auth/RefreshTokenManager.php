<?php

namespace Bluewing\Auth;

use Bluewing\Models\RefreshToken;
use Bluewing\Services\TokenGenerator;
use Bluewing\Contracts\AuthenticationContract;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class RefreshTokenManager {

    /**
     * An instance of `TokenGenerator`.
     *
     * @var TokenGenerator
     */
    protected $tokenGenerator;

    /**
     * Constructor for `RefreshTokenManagerTest`.
     *
     * @param TokenGenerator $tokenGenerator - A dependency-injected instance of `TokenGenerator`.
     */
    public function __construct(TokenGenerator $tokenGenerator) {
        $this->tokenGenerator = $tokenGenerator;
    }

    /**
     * Builds a refresh token for the specified `Authenticatable`, and inserts it into the database,
     * returning the `RefreshToken`'s token string.
     *
     * @param AuthenticationContract $authenticatable - The entity which implements the
     * authentication functionality.
     *
     * @return string - The `RefreshToken` string that can be exchanged for a new JSON web token later.
     *
     * @throws Exception
     */
    public function buildRefreshTokenFor(AuthenticationContract $authenticatable): string {
        $refreshToken = RefreshToken::create([
            'organizationId'        => $authenticatable->getTenant()->id,
            'userOrganizationId'    => $authenticatable->getAuthIdentifier(),
            'token'                 => $this->tokenGenerator->generate(32),
            'device'                => null
        ]);

        return $refreshToken->token;
    }

    /**
     * Finds the given `RefreshToken` in the database by the provided string. If no matching
     * token is found, throw a ModelNotFoundException.
     *
     * @param string $refreshTokenString - The string to find the `RefreshToken` by.
     *
     * @return RefreshToken - The `RefreshToken` entity that should've been retrieved.
     *
     * @throws ModelNotFoundException - If the `RefreshToken` cannot be found, this exception
     * will be thrown.
     */
    public function findRefreshTokenOrFail(string $refreshTokenString): RefreshToken {
        return RefreshToken::where('token', $refreshTokenString)->firstOrFail();
    }

    /**
     * Given a refresh token string, revokes the `RefreshToken` entry in the database.
     *
     * @param string $refreshTokenString - The string of the `RefreshToken` that should be deleted.
     *
     * @return void
     *
     * @throws Exception - An `Exception` will be thrown if the `RefreshToken` cannot be found.
     */
    public function revokeRefreshToken(string $refreshTokenString): void {
        $this->findRefreshTokenOrFail($refreshTokenString)->delete();
    }
}