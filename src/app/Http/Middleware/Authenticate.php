<?php

namespace Bluewing\Http\Middleware;

use Bluewing\Auth\Services\JwtManager;
use Closure;
use Illuminate\Auth\AuthManager;
use Illuminate\Http\Request;

/**
 * Class Authenticate
 *
 * TODO: Eventually replace the trait used in `Authenticate.php` middleware so the middleware can be used as so:
 * `auth:bluewing`.
 *
 * @package Bluewing\Middleware
 */
class Authenticate
{
    /**
     * @var JwtManager
     */
    public JwtManager $jwtManager;

    /**
     * @var AuthManager
     */
    public AuthManager $auth;

    /**
     * @var string
     */
    public string $jwtMemberKey = 'mid';

    /**
     * Constructor for Authenticate middleware.
     *
     * @param AuthManager $auth - The dependency-injected instance of `AuthManager`.
     * @param JwtManager $jwtManager - The dependency-injected instance of `JwtManager`.
     */
    public function __construct(AuthManager $auth, JwtManager $jwtManager)
    {
        $this->auth = $auth;
        $this->jwtManager = $jwtManager;
    }

    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param Closure $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($this->auth->guard()->check()) {
            return $next($request);
        }

        if (!$request->hasHeader('Authorization')) {
            return response()->json("No Authorization header provided", 401);
        }

        if (!$this->isAuthorizationHeaderVerifiable($request)) {
            return response()->json("Token provided is not verifiable", 401);
        }

        $userId = $this->jwtManager
                    ->jwtFromString($request->header('Authorization'))
                    ->getClaim($this->jwtMemberKey);

        $this->auth->guard()->setUserId($userId);

        return $next($request);
    }

    /**
     * Helper function to ensure an Authorization header verifies.
     *
     * @param Request $request
     *
     * @return bool - `true` if the Authorization header verifies successfully.
     */
    private function isAuthorizationHeaderVerifiable(Request $request) {
        return $this->jwtManager->isJwtVerified($request->header('Authorization'));
    }
}
