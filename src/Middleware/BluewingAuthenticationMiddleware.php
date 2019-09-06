<?php

namespace Bluewing\SharedServer\Middleware;

use Bluewing\SharedServer\Jwt\JwtManager;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Class BluewingAuthenticationMiddleware
 *
 * TODO: Eventually replace the trait used in `Authenticate.php` middleware so the middleware can be used as so:
 * `auth:bluewing`.
 *
 * @package App\Base\Middleware
 */
class BluewingAuthenticationMiddleware
{
    public $jwtManager;

    public function __construct(JwtManager $jwtManager)
    {
        $this->jwtManager = $jwtManager;
    }

    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!$request->hasHeader('Authorization')) {
            return response("No Authorization header provided", 401);
        }

        $authorizationHeaderString = $request->header('Authorization');

        if (!$this->jwtManager->isTokenVerified($authorizationHeaderString)) {
            return response("Token provided is not verifiable", 401);
        }

        $userId = $this->jwtManager->getToken()->getClaim('uid');
        Auth::setUserId($userId);

        return $next($request);
    }
}
