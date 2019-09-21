<?php

namespace Bluewing\Controllers;

use Bluewing\Auth\JwtManager;
use Bluewing\Auth\RefreshTokenManager;

/**
 *
 */
class TokenController extends Controller {

    /**
     * An instance of JwtManager.
     * 
     * @var JwtManager
     */
    protected $jwtManager;

    /**
     *
     */
    protected $refreshTokenManager;

    /**
     * Constructor for TokenController.
     *
     * @param JwtManager $jwtManager - The dependency-injected instance of `JwtManager`.
     * @param RefreshTokenManager $refreshTokenManager - The dependency-injected instance of `RefreshTokenManager`.
     */
    public function __construct(JwtManager $jwtManager, RefreshTokenManager $refreshTokenManager) {
        $this->jwtManager = $jwtManager;
        $this->refreshTokenManager = $refreshTokenManager;
    }

    /**
     * GET:/api/token
     *
     * Retrieves a new Access Token (JWT) by providing a refresh token in the body of the request.
     * If no `RefreshToken` is provided then the request fails.
     *
     * @return JsonResponse - 204 No Content, with the new JWT provided in the header of the response.
     */
    public function exchangeRefreshTokenForJwt() {
        if (!$request->has('refreshToken')) {
            return abort(401);
        }

        $refreshToken = $this->refreshTokenManager->findRefreshTokenOrFail($request->input('refreshToken'));

        $refreshToken = $this->refreshTokenManager->buildRefreshTokenFor($refreshToken->userOrganization);

        // Extend refresh token to be valid for another 7 days from this point.
        $refreshToken->touch();

        // Issue our response
        return response()
            ->json(null, 204)
            ->headers('Authorization', $refreshToken);
    }
}