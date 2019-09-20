<?php

namespace Bluewing\Controllers;

use Bluewing\Auth\RefreshTokenManager;

class LogoutController extends Controller {

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
     * @param Request $request - The `Request` object associated with the API endpoint.
     *
     * @return JsonResponse - 204 No Content when the logout request is processed successfully.
     */
    public function logout(Request $request) {

        $this->refreshTokenManager->revokeRefreshToken();

        return response()->json(null, 204);
    }
}