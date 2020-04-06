<?php

namespace Bluewing\Http\Controllers;

use Bluewing\Http\Requests\RefreshTokenRequest;
use Bluewing\Auth\JwtManager;
use Bluewing\Auth\RefreshTokenManager;
use Illuminate\Http\JsonResponse;
use Throwable;

/**
 *
 */
class TokenController extends Controller
{
    /**
     * An instance of `JwtManager`.
     *
     * @var JwtManager
     */
    protected JwtManager $jwtManager;

    /**
     * An instance of `RefreshTokenManager`.
     *
     * @var RefreshTokenManager
     */
    protected RefreshTokenManager $refreshTokenManager;

    /**
     * Constructor for TokenController.
     *
     * @param JwtManager $jwtManager - The dependency-injected instance of `JwtManager`.
     * @param RefreshTokenManager $refreshTokenManager - The dependency-injected instance of `RefreshTokenManagerTest`.
     */
    public function __construct(JwtManager $jwtManager, RefreshTokenManager $refreshTokenManager)
    {
        $this->jwtManager = $jwtManager;
        $this->refreshTokenManager = $refreshTokenManager;
    }

    /**
     * @http-method    POST
     * @url            /api/token
     *
     * Retrieves a new Access Token (JWT) by providing a refresh token in the body of the request.
     * If no `RefreshToken` is provided then the request fails.
     *
     * @param RefreshTokenRequest $request - The `Request` object associated with this API endpoint.
     *
     * @return JsonResponse - 204 No Content, with the new JWT provided in the header of the response.
     *
     * @throws Throwable
     */
    public function exchangeRefreshTokenForJwt(RefreshTokenRequest $request)
    {
        $refreshToken = $this->refreshTokenManager->findRefreshTokenForUse($request->input('refreshToken'));

        $jwt = $this->jwtManager->buildJwtFor($refreshToken->member);

        // Issue our response
        return response()
            ->json(null, 204)
            ->header('Authorization', $jwt)
            ->header('X-Refresh-Token', $refreshToken->token);
    }
}
