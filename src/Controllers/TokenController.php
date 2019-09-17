<?php

namespace Bluewing\Controllers;

use Bluewing\Models;

/**
 *
 */
class TokenController extends Controller {

    /**
     * An instance of JwtManager.
     */
    protected $jwtManager;

    /**
     * Constructor for AuthController.
     */
    public function __construct(JwtManager $jwtManager) {
        $this->jwtManager = $jwtManager;
    }

    /**
     * GET:/api/token
     *
     * Retrieves a new Access Token (JWT) by providing a refresh token
     * in the body of the request.
     *
     * @return JsonResponse - 204 No Content, with the new JWT provided
     * in the header of the response.
     */
    public function exchangeRefreshTokenForJwt() {
        if (!$request->has('refreshToken')) {
            return abort(401);
        }

        $refreshToken = RefreshToken::where('token', $request->has('refreshToken'))->first();
        $jwt = $this->jwtManager->buildTokenFor($refreshToken->userOrganization);

        // Extend refresh token to be valid for another 7 days from this point.
        $refreshToken->touch();

        // Issue our response
        return response()
            ->json(null, 204)
            ->headers('Authorization', $jwt);
    }
}