<?php

namespace Bluewing\Middleware;

use Closure;
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
     * @var AuthFacadeFactory
     */
    protected $auth;

    /**
     * An instance of `JwtManager`.
     *
     * @var JwtManager
     */
    protected $jwtManager;

    /**
     * An instance of `RefreshTokenManagerTest`.
     *
     * @var RefreshTokenManager
     */
    protected $refreshTokenManager;

    /**
     * Constructor for `AppendTokensToResponse` middleware.
     *
     * @param AuthFacadeFactory $auth - The dependency-injected instance of `Auth\Factory`.
     * @param JwtManager $jwtManager - The dependency-injected instance of `JwtManager`.
     * @param RefreshTokenManager $refreshTokenManager - The dependency-injected instance of `RefreshTokenManagerTest`.
     */
    public function __construct(AuthFacadeFactory $auth, JwtManager $jwtManager, RefreshTokenManager $refreshTokenManager) {
        $this->auth = $auth;
        $this->jwtManager = $jwtManager;
        $this->refreshTokenManager = $refreshTokenManager;
    }

    /**
     * Handles the response by adding both a JWT and a Refresh Token to the `Response` headers.
     *
     * @param Request $request - The `Request` that this middleware will process.
     * @param Closure $next - Handles executing functionality prior to this middleware.
     *
     * @return Response - The `Response` object to be passed to the client.
     *
     * @throws Exception
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
