<?php

namespace Bluewing\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthManager;
use Bluewing\Auth\Services\JwtManager;
use Bluewing\Auth\Services\RefreshTokenManager;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * "After" middleware that when requested, will append the authenticated user's request with
 * a new JSON Web Token and Refresh Token.
 */
class AppendTokensToResponse {

    /**
     * Constructor for `AppendTokensToResponse` middleware.
     *
     * @param AuthManager $auth - An instance of the `Auth\Factory` contract that is dependency-injected into the class.
     * @param JwtManager $jwtManager - The dependency-injected instance of `JwtManager`.
     * @param RefreshTokenManager $refreshTokenManager - The dependency-injected instance of `RefreshTokenManager`.
     */
    public function __construct(protected AuthManager $auth, protected JwtManager $jwtManager, protected RefreshTokenManager $refreshTokenManager) {}

    /**
     * Handles the response by adding both a JWT and a Refresh Token to the `Response` headers. This is utilised
     * where the incoming user is going from an unauthenticated state to an authenticated one, like during login
     * or email verification.
     *
     * @param Request $request - The `Request` that this middleware will process.
     * @param Closure $next - Handles executing functionality prior to this middleware.
     *
     * @return Response - The `Response` object to be passed to the client.
     *
     * @throws Exception
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        if ($request->hasHeader('Authorization') || !$this->auth->user()) {
            return $response;
        }

        $response->withHeaders([
            'Authorization'     => $this->jwtManager->buildJwtFor($this->auth->user()),
            'X-Refresh-Token'   => $this->refreshTokenManager->buildRefreshTokenFor($this->auth->user())
        ]);

        return $response;
    }
}
