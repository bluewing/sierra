<?php

namespace Bluewing\Http\Middleware;

use Bluewing\Auth\Services\JwtManager;
use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\AuthManager;
use Illuminate\Http\Request;
use Lcobucci\JWT\Token\RegisteredClaims;

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
     * Constructor for Authenticate middleware.
     *
     * @param AuthManager $auth - The dependency-injected instance of `AuthManager`.
     * @param JwtManager $jwtManager - The dependency-injected instance of `JwtManager`.
     */
    public function __construct(public AuthManager $auth, public JwtManager $jwtManager) {}

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     *
     * @return mixed
     *
     * @throws AuthenticationException - An `AuthenticationException` will be thrown if the request does not have an
     * "Authorization" header, or the token present in the "Authorization" header cannot be verified.
     */
    public function handle($request, Closure $next)
    {
        if ($this->auth->guard()->check()) {
            return $next($request);
        }

        if (!$request->hasHeader('Authorization')) {
            throw new AuthenticationException('No Authorization header provided');
        }

        $authHeader = $request->header('Authorization');

        if (!$this->jwtManager->isJwtVerified($authHeader)) {
            throw new AuthenticationException('Token provided is not verifiable');
        }

        $this->auth->guard()->setUserId(
            $this->jwtManager->jwtFromHeader($authHeader)->claims()->get(RegisteredClaims::SUBJECT)
        );
        return $next($request);
    }
}
