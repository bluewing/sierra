<?php

namespace Bluewing\Middleware;

use Bluewing\Auth\JwtManager;
use Closure;
use Illuminate\Http\JsonResponse;
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
    public $jwtManager;

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
     * @param  \Closure  $next
     *
     * @return mmixed
     */
    public function handle($request, Closure $next)
    {
        if (!$request->hasHeader('Authorization')) {
            return response()->json("No Authorization header provided", 401);
        }

        $authorizationHeaderString = $request->header('Authorization');

        if (!$this->jwtManager->isJwtVerified($authorizationHeaderString)) {
            return response()->json("Token provided is not verifiable", 401);
        }

        $userId = $this->jwtManager->jwtFromString($authorizationHeaderString)->getClaim('uid');
        Auth::setUserId($userId);

        return $next($request);
    }
}
