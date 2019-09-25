<?php

namespace Bluewing\Auth;

use Bluewing\Contracts\UserOrganizationContract;
use Bluewing\Services\TokenGenerator;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class RefreshTokenManager {

    /**
     * An instance of `TokenGenerator`.
     *
     * @var TokenGenerator
     */
    protected $tokenGenerator;

    /**
     * An instance of the `RefreshToken` model used to query the database.
     *
     * @var Model
     */
    protected $refreshTokenModel;

    /**
     * Constructor for `RefreshTokenManager`.
     *
     * @param TokenGenerator $tokenGenerator - A dependency-injected instance of `TokenGenerator`.
     * @param string model - The name of the model to inject.
     */
    public function __construct(TokenGenerator $tokenGenerator, string $model) {
        $this->tokenGenerator = $tokenGenerator;
        $this->refreshTokenModel = $this->createModel($model);
    }

    /**
     * Builds a refresh token for the specified `UserOrganizationContract`, and inserts it into the database,
     * returning the `RefreshToken`'s token string.
     *
     * @param UserOrganizationContract $authenticatable - The entity which implements the
     * authentication functionality (in our case, `UserOrganization`).
     *
     * @return string - The `RefreshToken` string that can be exchanged for a new JSON web token.
     *
     * @throws Exception
     */
    public function buildRefreshTokenFor(UserOrganizationContract $authenticatable): string {
        $refreshToken = $this->refreshTokenModel->newQuery()->create([
            'organizationId'        => $authenticatable->getTenant()->id,
            'userOrganizationId'    => $authenticatable->getAuthIdentifier(),
            'token'                 => $this->tokenGenerator->generate(64),
            'device'                => null
        ]);

        return $refreshToken->token;
    }

    /**
     * Finds the given `RefreshToken` in the database by the provided string, if it exists, touch the
     * `RefreshToken` to extend its longevity. If no matching token is found, throw a `ModelNotFoundException`.
     *
     * @param string $refreshTokenString - The token string used to find the `RefreshToken`.
     *
     * @return Model - The `RefreshToken` entity that should've been retrieved.
     *
     * @throws ModelNotFoundException - If the `RefreshToken` cannot be found, this exception
     * will be thrown.
     */
    public function findRefreshTokenOrFail(string $refreshTokenString): Model {
        $refreshToken = $this->refreshTokenModel
            ->newQuery()
            ->where('token', $refreshTokenString)
            ->firstOrFail();

        $refreshToken->touch();

        return $refreshToken;
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

    /**
     * Find and delete all `RefreshToken` entries where the last utilised date was a week ago.
     *
     * This functionality is utilised by the `DeleteExpiredRefreshTokensJob` class, which is set
     * to run every hour as a cron task.
     *
     * @return void
     */
    public function deleteAllExpiredRefreshTokens(): void {
        $this->refreshTokenModel
            ->newQuery()
            ->where('updatedAt', '<', Carbon::now()->subWeek())
            ->delete();
    }

    /**
     * Create a new instance of the model.
     *
     * @param string $model
     *
     * @return Model
     */
    private function createModel(string $model)
    {
        $class = '\\'.ltrim($model, '\\');

        return new $class;
    }
}
