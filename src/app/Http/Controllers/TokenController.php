<?php

namespace Bluewing\Http\Controllers;

use Bluewing\Http\Requests\RefreshTokenRequest;
use Bluewing\Auth\Services\JwtManager;
use Bluewing\Auth\Services\RefreshTokenManager;
use Illuminate\Http\JsonResponse;
use Throwable;

/**
 *
 */
class TokenController extends Controller
{
    /**
     * Constructor for `TokenController`.
     *
     * @param JwtManager $jwtManager - The dependency-injected instance of `JwtManager`.
     * @param RefreshTokenManager $refreshTokenManager - The dependency-injected instance of `RefreshTokenManagerTest`.
     */
    public function __construct(protected JwtManager $jwtManager, protected RefreshTokenManager $refreshTokenManager) {}

    /**
     * @http-method    POST
     * @endpoint       /api/token
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
    public function exchangeRefreshTokenForJwt(RefreshTokenRequest $request): JsonResponse
    {
        $refreshToken = $this->refreshTokenManager->findRefreshTokenForUse(
            $request->input(RefreshTokenRequest::REFRESH_TOKEN_KEY)
        );

        $jwt = $this->jwtManager->buildJwtFor($refreshToken->member);

        // Issue our response
        return response()
            ->json(null, 204)
            ->header('Authorization', $jwt)
            ->header('X-Refresh-Token', $refreshToken->token);
    }
}
