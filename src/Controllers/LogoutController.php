<?php

namespace Bluewing\Controllers;

use Bluewing\Auth\RefreshTokenManager;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LogoutController extends Controller {

    /**
     * The instance of `RefreshTokenManagerTest`.
     */
    protected $refreshTokenManager;

    /**
     * Constructor for LogoutController.
     *
     * @param RefreshTokenManager $refreshTokenManager - The dependency-injected instance of `RefreshTokenManagerTest`.
     */
    public function __construct(RefreshTokenManager $refreshTokenManager)
    {
        $this->refreshTokenManager = $refreshTokenManager;
    }

    /**
     * @bluewing-http-method    POST
     * @bluewing-url            /api/auth/logout
     * @bluewing-auth           Authenticated
     *
     * Logs a user out of the application by revoking their `RefreshToken` associated with the
     * current session.
     *
     * @param Request $request - The `Request` object associated with the API endpoint.
     *
     * @return JsonResponse - 204 No Content when the logout request is processed successfully.
     *
     * @throws Exception
     */
    public function logout(Request $request)
    {
        $this->refreshTokenManager->revokeRefreshToken($request->input('refreshToken'));
        return response()->json(null, 204);
    }
}
