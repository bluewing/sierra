<?php

namespace Bluewing\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Factory;
use Bluewing\Auth\JwtManager;
use Bluewing\Auth\RefreshTokenManager;

/**
 * "After" middleware that when requested, will append the authenticated user's request with
 * a new JSON Web Token and Refresh Token.
 */
class AppendTokensToResponse {

    /**
     * An instance of the `Auth\Factory` contract that is dependency-injected into the class.
     */
    protected $auth;

    /**
     * An instance of `JwtManager`.
     */
    protected $jwtManager;

    /**
     * An instance of `RefreshTokenManager`.
     */
    protected $refreshTokenManager;

    /**
     * Constructor for `AppendTokensToResponse` middleware.
     *
     * @param Factory $auth - The dependency-injected instance of `Auth\Factory`.
     * @param JwtManager $jwtManager - The dependency-injected instance of `JwtManager`.
     * @param RefreshTokenManager $refreshTokenManager - The dependency-injected instance of `RefreshTokenManager`.
     */
    public function __construct(Factory $auth, JwtManager $jwtManager, RefreshTokenManager $refreshTokenManager) {
        $this->auth = $auth;
        $this->jwtManager = $jwtManager;
        $this->refreshTokenManager = $refreshTokenManager;
    }

    /**
     * Handles the response by adding both a JWT and a
     *
     * @param Request $request -
     * @param Closure $next -
     *
     * @return
     */
    public function handle($request, Closure $next) {
        $response = $next($request);

        if ($request->headers->has('Authorization') || !$this->auth->user()) {
            return $response;
        }

        $response->withHeaders([
            'Authorization'     => $this->jwtManager->buildJwtFor($this->auth->user()),
            'X-Refresh-Token'   => $this->refreshTokenManager->buildRefreshTokenFor($this->auth->user())
        ]);

        return $response;
    }
}