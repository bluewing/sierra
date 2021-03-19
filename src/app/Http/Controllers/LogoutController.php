<?php

namespace Bluewing\Http\Controllers;

use Bluewing\Auth\Services\RefreshTokenManager;
use Bluewing\Http\Requests\RefreshTokenRequest;
use Exception;
use Illuminate\Http\JsonResponse;

class LogoutController extends Controller
{
    /**
     * Constructor for `LogoutController`.
     *
     * @param RefreshTokenManager $refreshTokenManager - The dependency-injected instance of `RefreshTokenManager`.
     */
    public function __construct(protected RefreshTokenManager $refreshTokenManager) {}

    /**
     * @http-method     POST
     * @endpoint        /api/auth/logout
     *
     * Logs a user out of the application by revoking their `RefreshToken` associated with the
     * current session.
     *
     * @param RefreshTokenRequest $request - The `RefreshTokenRequest` object associated with the API endpoint.
     *
     * @return JsonResponse - 204 No Content when the logout request is processed successfully.
     *
     * @throws Exception
     */
    public function __invoke(RefreshTokenRequest $request): JsonResponse
    {
        $this->refreshTokenManager->revokeRefreshToken($request->input(RefreshTokenRequest::REFRESH_TOKEN_KEY));
        return response()->json(null, 204);
    }
}
