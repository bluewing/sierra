<?php

namespace Bluewing\Auth\Services;

use Bluewing\Auth\Contracts\Claimable;
use Bluewing\Services\TokenGenerator;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Throwable;

class RefreshTokenManager
{
    /**
     * Constructor for `RefreshTokenManager`.
     *
     * @param TokenGenerator $tokenGenerator - A dependency-injected instance of `TokenGenerator`.
     * @param Model $refreshTokenModel - An instance of the `RefreshToken` model used to query the database.
     */
    public function __construct(protected TokenGenerator $tokenGenerator, protected Model $refreshTokenModel) {}

    /**
     * Builds a refresh token for the specified `Claimable`, and inserts it into the database, returning the
     * `RefreshToken`'s token string.
     *
     * @param Claimable $claimable - The entity which implements the authentication functionality (in our case,
     * `Member`).
     *
     * @return string - The `RefreshToken` string that can be exchanged for a new JSON web token.
     *
     * @throws Exception
     */
    public function buildRefreshTokenFor(Claimable $claimable): string
    {
        $refreshToken = $this->refreshTokenModel->newQuery()->create([
            'organizationId'    => $claimable->getTenancyIdentifier(),
            'memberId'          => $claimable->getAuthIdentifier(),
            'token'             => $this->tokenGenerator->generate(64, 'refresh'),
            'device'            => null
        ]);

        return $refreshToken->token;
    }

    /**
     * Finds a given refresh token in the database by the provided refresh token string.
     *
     * @param string $refreshTokenString - The token string used to find the `RefreshToken`.
     *
     * @return Model|null - The `RefreshToken`, if it exists. If it does not exist, returns `null`.
     */
    public function findRefreshToken(string $refreshTokenString): ?Model
    {
        return $this->refreshTokenModel->newQuery()
            ->where('token', $refreshTokenString)
            ->first();
    }

    /**
     * Finds the given `RefreshToken` in the database by the provided string, if it exists, touch the
     * `RefreshToken` to extend its longevity. If no matching token is found, throw a `ModelNotFoundException`.
     *
     * @param string $refreshTokenString - The token string used to find the `RefreshToken`.
     *
     * @return Model - The `RefreshToken` entity that should've been retrieved.
     *
     * @throws Throwable - If the `RefreshToken` cannot be found, `ModelNotFoundException` will be thrown.
     */
    public function findRefreshTokenForUse(string $refreshTokenString): Model
    {
        $refreshToken = $this->findRefreshToken($refreshTokenString);

        throw_if(is_null($refreshToken), ModelNotFoundException::class);

        $refreshToken->increment('uses');
        $refreshToken->touch();

        return $refreshToken;
    }

    /**
     * Given a refresh token string, revokes the `RefreshToken` entry in the database, if it exists.
     *
     * @param string $refreshTokenString - The string of the `RefreshToken` that should be deleted.
     *
     * @return void
     *
     * @throws Exception
     */
    public function revokeRefreshToken(string $refreshTokenString): void
    {
        $this->findRefreshToken($refreshTokenString)?->delete();
    }

    /**
     * Find and delete all `RefreshToken` entries where the last utilised date was a week ago. This functionality is
     * utilised by the `DeleteExpiredRefreshTokensJob` class, which is set to run every hour as a cron task.
     *
     * @return void
     */
    public function deleteAllExpiredRefreshTokens(): void
    {
        $this->refreshTokenModel
            ->newQuery()
            ->where($this->refreshTokenModel->getUpdatedAtColumn(), '<', Carbon::now()->subWeek())
            ->delete();
    }
}
