<?php

namespace Bluewing\Controllers;

use Bluewing\Auth\RefreshTokenManager;

class LogoutController extends Controller {

    /**
     * The instance of `RefreshTokenManager`.
     */
    protected $refreshTokenManager;

    /**
     * Constructor for LogoutController.
     *
     * @param RefreshTokenManager $refreshTokenManager - The dependency-injected instance of `RefreshTokenManager`.
     */
    public function __construct(RefreshTokenManager $refreshTokenManager) {
        $this->refreshTokenManager = $refreshTokenManager;
    }

    /**
     * POST:/api/auth/logout
     * 
     * Logs a user out of the applicatin by revoking their `RefreshToken` associated with the 
     * current session.
     *
     * @param Request $request - The `Request` object associated with the API endpoint.
     *
     * @return JsonResponse - 204 No Content when the logout request is processed successfully.
     */
    public function logout(Request $request) {
        $this->refreshTokenManager->revokeRefreshToken($request->input('refreshToken'));
        return response()->json(null, 204);
    }
}