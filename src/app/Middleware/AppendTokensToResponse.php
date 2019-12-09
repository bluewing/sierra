<?php

namespace Bluewing\Middleware;

use Closure;
use Illuminate\Auth\AuthManager;
use Illuminate\Contracts\Auth\Factory as AuthFacadeFactory;
use Bluewing\Auth\JwtManager;
use Bluewing\Auth\RefreshTokenManager;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * "After" middleware that when requested, will append the authenticated user's request with
 * a new JSON Web Token and Refresh Token.
 */
class AppendTokensToResponse {

    /**
     * An instance of the `Auth\Factory` contract that is dependency-injected into the class.
     *
     * @var AuthManager
     */
    protected AuthManager $auth;

    /**
     * An instance of `JwtManager`.
     *
     * @var JwtManager
     */
    protected JwtManager $jwtManager;

    /**
     * An instance of `RefreshTokenManagerTest`.
     *
     * @var RefreshTokenManager
     */
    protected RefreshTokenManager $refreshTokenManager;

    /**
     * Constructor for `AppendTokensToResponse` middleware.
     *
     * @param AuthManager $auth - The dependency-injected instance of `AuthManager`.
     * @param JwtManager $jwtManager - The dependency-injected instance of `JwtManager`.
     * @param RefreshTokenManager $refreshTokenManager - The dependency-injected instance of `RefreshTokenManagerTest`.
     */
    public function __construct(AuthManager $auth, JwtManager $jwtManager, RefreshTokenManager $refreshTokenManager)
    {
        $this->auth = $auth;
        $this->jwtManager = $jwtManager;
        $this->refreshTokenManager = $refreshTokenManager;
    }

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
