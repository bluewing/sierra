<?php

namespace Bluewing\Middleware;

use Bluewing\Auth\JwtManager;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
     * Constructor for Authenticate middleware.
     * 
     * @param JwtManager $jwtManager - The dependency-injected instance of `JwtManager`.
     */
    public function __construct(JwtManager $jwtManager)
    {
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
        if (!$request->hasHeader('Authorization')) {
            return response()->json("No Authorization header provided", 401);
        }

        if (!$this->isAuthorizationHeaderVerifiable($request)) {
            return response()->json("Token provided is not verifiable", 401);
        }

        $userId = $this->jwtManager->jwtFromString($request->header('Authorization'))->getClaim('uid');
        Auth::setUserId($userId);

        return $next($request);
    }

    /**
     * Helper function to ensure an Authorization header verifies.
     *
     * @param $request
     *
     * @return bool - `true` if the Authorization header verifies successfully.
     */
    private function isAuthorizationHeaderVerifiable($request) {
        return $this->jwtManager->isJwtVerified($request->header('Authorization'));
    }
}
